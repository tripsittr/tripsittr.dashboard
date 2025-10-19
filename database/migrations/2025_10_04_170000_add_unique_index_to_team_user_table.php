<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Remove perfect duplicates first keeping the lowest id
        DB::statement(<<<SQL
            DELETE tu1 FROM team_user tu1
            INNER JOIN team_user tu2 ON tu1.team_id = tu2.team_id AND tu1.user_id = tu2.user_id AND tu1.id > tu2.id;
        SQL);

        // Add unique composite index to prevent future duplicates
        if (! $this->indexAlreadyExists('team_user', 'team_user_team_id_user_id_unique')) {
            Schema::table('team_user', function (Blueprint $table) {
                try {
                    $table->unique(['team_id','user_id'], 'team_user_team_id_user_id_unique');
                } catch(\Throwable $e) {
                    // Ignore if race condition or already exists
                }
            });
        }
    }

    public function down(): void {
        if ($this->indexAlreadyExists('team_user', 'team_user_team_id_user_id_unique')) {
            Schema::table('team_user', function (Blueprint $table) {
                try { $table->dropUnique('team_user_team_id_user_id_unique'); } catch(\Throwable $e) {}
            });
        }
    }

    private function indexAlreadyExists(string $table, string $index): bool {
        $database = DB::getDatabaseName();
        $count = DB::table('information_schema.STATISTICS')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', $table)
            ->where('INDEX_NAME', $index)
            ->count();
        return $count > 0;
    }
};
