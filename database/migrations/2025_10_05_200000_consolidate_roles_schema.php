<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('roles')) {
            return;
        }

        // 1. Detect current primary key column
        $currentPk = null;
        try {
            $res = DB::selectOne(<<<SQL
                SELECT k.COLUMN_NAME
                FROM information_schema.TABLE_CONSTRAINTS t
                JOIN information_schema.KEY_COLUMN_USAGE k
                  ON t.CONSTRAINT_NAME = k.CONSTRAINT_NAME
                 AND t.TABLE_SCHEMA = DATABASE()
                 AND t.TABLE_NAME = k.TABLE_NAME
               WHERE t.TABLE_SCHEMA = DATABASE()
                 AND t.TABLE_NAME = 'roles'
                 AND t.CONSTRAINT_TYPE = 'PRIMARY KEY'
               LIMIT 1
            SQL);
            $currentPk = $res->COLUMN_NAME ?? null;
        } catch (Throwable $e) { /* ignore */ }

        $hasPkColumn = Schema::hasColumn('roles', 'pk');

        // 2. If surrogate pk was introduced, revert to canonical `id` auto increment PK.
        if ($hasPkColumn) {
            try {
                if ($currentPk === 'pk') {
                    // Promote id back if needed
                    DB::statement('ALTER TABLE roles DROP PRIMARY KEY');
                    // Ensure id is NOT NULL
                    try { DB::statement('ALTER TABLE roles MODIFY id BIGINT UNSIGNED NOT NULL'); } catch (Throwable $e) {}
                    // If id is not auto_increment, attempt to set it
                    try { DB::statement('ALTER TABLE roles MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT'); } catch (Throwable $e) {}
                    DB::statement('ALTER TABLE roles ADD PRIMARY KEY (id)');
                }
            } catch (Throwable $e) {
                logger()->warning('Consolidation: could not restore roles PK to id: '.$e->getMessage());
            }
            // Drop surrogate column
            try {
                Schema::table('roles', function (Blueprint $table) {
                    if (Schema::hasColumn('roles','pk')) {
                        $table->dropColumn('pk');
                    }
                });
            } catch (Throwable $e) { logger()->warning('Consolidation: drop pk column failed: '.$e->getMessage()); }
        }

        // 3. Clean pivot tables of role_pk artifacts
        foreach (['model_has_roles','role_has_permissions'] as $pivot) {
            if (! Schema::hasTable($pivot)) continue;
            if (Schema::hasColumn($pivot,'role_pk')) {
                // Drop foreign key if exists
                foreach ([
                    $pivot.'_role_pk_foreign', // our earlier name
                    'role_has_permissions_role_pk_foreign',
                    'model_has_roles_role_pk_foreign',
                ] as $fk) {
                    try { DB::statement("ALTER TABLE `{$pivot}` DROP FOREIGN KEY `{$fk}`"); } catch (Throwable $e) { /* ignore */ }
                }
                try {
                    Schema::table($pivot, function (Blueprint $table) use ($pivot) {
                        if (Schema::hasColumn($pivot,'role_pk')) {
                            $table->dropColumn('role_pk');
                        }
                    });
                } catch (Throwable $e) { logger()->warning("Consolidation: drop role_pk in {$pivot} failed: ".$e->getMessage()); }
            }
            // Drop stray indexes
            foreach (['mhr_role_pk_idx','rhp_role_pk_idx'] as $idx) {
                try { DB::statement("DROP INDEX `{$idx}` ON `{$pivot}`"); } catch (Throwable $e) { /* ignore */ }
            }
        }

        // 4. Remove backup or legacy indexes if present
        foreach (['roles_id_unique_backup'] as $idx) {
            try { DB::statement("DROP INDEX `{$idx}` ON roles"); } catch (Throwable $e) { /* ignore */ }
        }

        // 5. Ensure composite uniqueness for team scoping if team_id exists
        $hasTeamId = Schema::hasColumn('roles','team_id');
        if ($hasTeamId) {
            // Drop any old unique combos we replaced
            try { DB::statement('ALTER TABLE roles DROP INDEX roles_name_guard_name_unique'); } catch (Throwable $e) { /* ignore */ }
            // Create desired index if missing
            try { DB::statement('ALTER TABLE roles ADD UNIQUE INDEX roles_team_id_name_guard_name_unique (team_id, name, guard_name)'); } catch (Throwable $e) { /* ignore */ }
        } else {
            try { DB::statement('ALTER TABLE roles ADD UNIQUE INDEX roles_name_guard_name_unique (name, guard_name)'); } catch (Throwable $e) { /* ignore */ }
        }
    }

    public function down(): void
    {
        // Non-destructive: nothing to reintroduce; this consolidation is effectively irreversible.
        // Optionally we could re-add surrogate pk but that's intentionally omitted.
    }
};
