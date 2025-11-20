<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'action',
            'albums',
            'events',
            'inventory_items',
            'knowledge',
            'songs',
            'teams',
            'team_user',
            'user_actions',
            'user_activities',
            'user_integrations',
            'users',
            'venues',
        ];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                if (! Schema::hasColumn($table->getTable(), 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'action',
            'albums',
            'events',
            'inventory_items',
            'knowledge',
            'songs',
            'teams',
            'team_user',
            'user_actions',
            'user_activities',
            'user_integrations',
            'users',
            'venues',
        ];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'deleted_at')) {
                    $table->dropSoftDeletes();
                }
            });
        }
    }
};
