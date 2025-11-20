<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * List of tables to ensure have a team_id column.
     * Add any additional tables referenced by the codebase that should be team-scoped.
     *
     * Note: migration is defensive — it checks for table/column existence before altering.
     */
    protected array $tables = [
        'albums',
        'songs',
        'events',
        'activity_logs',
        'customers',
        'approvals',
        'direct_message_threads',
        'user_actions',
        'catalog_items',
        'orders',
        'inventory_items',
        'venues',
        'venue_events',
        'albums',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (! Schema::hasTable($tableName)) {
                // table doesn't exist in this database, skip
                continue;
            }

            if (Schema::hasColumn($tableName, 'team_id')) {
                // already has column
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->unsignedBigInteger('team_id')->nullable()->index()->after('id');
            });

            // try to add foreign key constraint if teams table exists
            if (Schema::hasTable('teams')) {
                try {
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->foreign('team_id')->references('id')->on('teams')->nullOnDelete();
                    });
                } catch (\Throwable $e) {
                    // ignore foreign key errors (e.g., older MySQL versions, differing naming)
                    // we don't want migrations to fail if FK cannot be added
                }
            }
        }

        // Special: ensure roles/permissions/spatie tables already have team_id via package, but we won't touch them here.
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            if (! Schema::hasColumn($tableName, 'team_id')) {
                continue;
            }

            // attempt to drop FK first
            try {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $fk = $tableName . '_team_id_foreign';
                    // attempt to drop using conventional name, if exists
                    $table->dropForeign($fk);
                });
            } catch (\Throwable $e) {
                // ignore
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('team_id');
            });
        }
    }
};
