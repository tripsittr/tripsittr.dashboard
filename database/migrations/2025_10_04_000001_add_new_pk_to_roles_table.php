<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Safety checks
        if (! Schema::hasTable('roles')) {
            return; // nothing to do
        }

        // 1. Add new surrogate column (nullable for now)
        if (! Schema::hasColumn('roles', 'pk')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->unsignedBigInteger('pk')->nullable()->after('id');
            });
        }

        // 2. Populate pk with existing id values (or custom sequence)
        DB::table('roles')->whereNull('pk')->orderBy('id')->chunkById(500, function($rows){
            foreach ($rows as $r) {
                DB::table('roles')->where('id', $r->id)->update(['pk' => $r->id]);
            }
        });

        // 3. Ensure uniqueness of old id (backup) before changing primary key
        // (If there's already a unique index combining name+guard, that's fine; we add explicit one for clarity.)
        // Add backup unique index on id if it does not already exist (best-effort; ignore errors if exists)
        try {
            DB::statement('CREATE UNIQUE INDEX roles_id_unique_backup ON roles(id)');
        } catch (Throwable $e) { /* index probably exists */ }

        // 4. Drop FKs referencing roles.id and add parallel pk columns in referencing tables
        foreach (['model_has_roles','role_has_permissions','model_has_permissions'] as $pivot) {
            if (! Schema::hasTable($pivot)) continue;
            // Add new fk column if missing
            $newCol = 'role_pk';
            if ($pivot === 'model_has_permissions') {
                // This table references permissions, skip here
                continue;
            }
            if (! Schema::hasColumn($pivot, $newCol)) {
                Schema::table($pivot, function (Blueprint $table) use ($newCol) {
                    $table->unsignedBigInteger($newCol)->nullable()->after('role_id');
                });
            }
        }

        // 5. Copy role_id into role_pk for pivot tables
        if (Schema::hasTable('model_has_roles') && Schema::hasColumn('model_has_roles','role_pk')) {
            DB::table('model_has_roles')->whereNull('role_pk')->update(['role_pk' => DB::raw('role_id')]);
        }
        if (Schema::hasTable('role_has_permissions') && Schema::hasColumn('role_has_permissions','role_pk')) {
            DB::table('role_has_permissions')->whereNull('role_pk')->update(['role_pk' => DB::raw('role_id')]);
        }

        // 6. Create indexes on new fk columns before FK creation
        // Create indexes on new fk columns before FK creation (skip if already exists)
        try {
            Schema::table('model_has_roles', function (Blueprint $table) {
                if (Schema::hasColumn('model_has_roles','role_pk')) {
                    $table->index('role_pk','mhr_role_pk_idx');
                }
            });
        } catch (Throwable $e) { /* index exists */ }
        try {
            Schema::table('role_has_permissions', function (Blueprint $table) {
                if (Schema::hasColumn('role_has_permissions','role_pk')) {
                    $table->index('role_pk','rhp_role_pk_idx');
                }
            });
        } catch (Throwable $e) { /* index exists */ }

        // 7. Drop existing foreign keys referencing roles.id (names may vary; use try/catch)
        $dropFkIfExists = function(string $table, string $fkName) {
            try { DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$fkName}`"); } catch (Throwable $e) { /* ignore */ }
        };
        $dropFkIfExists('model_has_roles','model_has_roles_role_id_foreign');
        $dropFkIfExists('role_has_permissions','role_has_permissions_role_id_foreign');

        // 8. Safely attempt to swap primary key ONLY if current PK is on `id` and not already switched.
        try {
            $primaryInfo = DB::selectOne(<<<SQL
                SELECT k.COLUMN_NAME
                FROM information_schema.TABLE_CONSTRAINTS t
                JOIN information_schema.KEY_COLUMN_USAGE k
                  ON t.CONSTRAINT_NAME = k.CONSTRAINT_NAME
                 AND t.TABLE_SCHEMA = k.TABLE_SCHEMA
                 AND t.TABLE_NAME = k.TABLE_NAME
                WHERE t.TABLE_SCHEMA = DATABASE()
                  AND t.TABLE_NAME = 'roles'
                  AND t.CONSTRAINT_TYPE = 'PRIMARY KEY'
                LIMIT 1
            SQL);

            $currentPkColumn = $primaryInfo->COLUMN_NAME ?? null;

            // If no PK or already not on `id`, skip changing structure.
            if ($currentPkColumn === 'id') {
                // Make sure pk column populated
                $missingPk = DB::table('roles')->whereNull('pk')->count();
                if ($missingPk === 0) {
                    try { DB::statement('ALTER TABLE roles DROP PRIMARY KEY'); } catch (Throwable $e) { /* might already be dropped */ }
                    try { DB::statement('ALTER TABLE roles MODIFY id BIGINT UNSIGNED NOT NULL'); } catch (Throwable $e) { /* ignore */ }
                    try { DB::statement('ALTER TABLE roles MODIFY pk BIGINT UNSIGNED NOT NULL AUTO_INCREMENT'); } catch (Throwable $e) { /* ignore */ }
                    try { DB::statement('ALTER TABLE roles ADD PRIMARY KEY (pk)'); } catch (Throwable $e) { /* ignore */ }
                }
            }
        } catch (Throwable $e) {
            // Log but don't fail the whole migration; primary key swap is optional.
            logger()->warning('Roles PK swap skipped: '.$e->getMessage());
        }

        // 9. Add new foreign keys referencing roles.pk
        try {
            DB::statement('ALTER TABLE model_has_roles ADD CONSTRAINT model_has_roles_role_pk_foreign FOREIGN KEY (role_pk) REFERENCES roles(pk) ON DELETE CASCADE');
        } catch (Throwable $e) { /* ignore */ }
        try {
            DB::statement('ALTER TABLE role_has_permissions ADD CONSTRAINT role_has_permissions_role_pk_foreign FOREIGN KEY (role_pk) REFERENCES roles(pk) ON DELETE CASCADE');
        } catch (Throwable $e) { /* ignore */ }

        // 10. (Optional) Rename columns so code can keep using role_id
        // We keep both for now to avoid breaking code; developer can later migrate uses.
    }

    public function down(): void
    {
        // Attempt best-effort reversal (complex; may not perfectly restore original state if data changed)
        if (! Schema::hasTable('roles') || ! Schema::hasColumn('roles','pk')) return;

        // Drop new foreign keys
        foreach ([
            ['model_has_roles','model_has_roles_role_pk_foreign'],
            ['role_has_permissions','role_has_permissions_role_pk_foreign'],
        ] as [$table,$fk]) {
            try { DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$fk}`"); } catch (Throwable $e) { /* ignore */ }
        }

        // Restore original primary key if possible
        try { DB::statement('ALTER TABLE roles DROP PRIMARY KEY'); } catch (Throwable $e) { /* ignore */ }
        try { DB::statement('ALTER TABLE roles ADD PRIMARY KEY (id)'); } catch (Throwable $e) { /* ignore */ }

        // Drop added indexes
        try { DB::statement('DROP INDEX roles_id_unique_backup ON roles'); } catch (Throwable $e) { /* ignore */ }
        try { DB::statement('DROP INDEX mhr_role_pk_idx ON model_has_roles'); } catch (Throwable $e) { /* ignore */ }
        try { DB::statement('DROP INDEX rhp_role_pk_idx ON role_has_permissions'); } catch (Throwable $e) { /* ignore */ }

        // Drop surrogate column
        Schema::table('roles', function (Blueprint $table) {
            if (Schema::hasColumn('roles','pk')) {
                $table->dropColumn('pk');
            }
        });
        foreach (['model_has_roles','role_has_permissions'] as $pivot) {
            Schema::table($pivot, function (Blueprint $table) use ($pivot) {
                if (Schema::hasColumn($pivot,'role_pk')) {
                    $table->dropColumn('role_pk');
                }
            });
        }
    }
};
