<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Adjust unique constraint to allow same role name per team
        Schema::table('roles', function (Blueprint $table) {
            // Drop old unique if exists
            try {
                $table->dropUnique('roles_name_guard_name_unique');
            } catch (Throwable $e) { /* ignore */
            }
            // Add composite unique including team_id
            $table->unique(['team_id', 'name', 'guard_name'], 'roles_team_id_name_guard_name_unique');
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            try {
                $table->dropUnique('roles_team_id_name_guard_name_unique');
            } catch (Throwable $e) { /* ignore */
            }
            $table->unique(['name', 'guard_name'], 'roles_name_guard_name_unique');
        });
    }
};
