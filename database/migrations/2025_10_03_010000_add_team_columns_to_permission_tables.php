<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('roles', 'team_id')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->unsignedBigInteger('team_id')->nullable()->index()->after('id');
            });
        }
        foreach (['model_has_permissions', 'model_has_roles'] as $pivot) {
            if (! Schema::hasColumn($pivot, 'team_id')) {
                Schema::table($pivot, function (Blueprint $table) use ($pivot) {
                    $table->unsignedBigInteger('team_id')->nullable()->index()->after('model_id');
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('roles', 'team_id')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropColumn('team_id');
            });
        }
        foreach (['model_has_permissions', 'model_has_roles'] as $pivot) {
            if (Schema::hasColumn($pivot, 'team_id')) {
                Schema::table($pivot, function (Blueprint $table) use ($pivot) {
                    $table->dropColumn('team_id');
                });
            }
        }
    }
};
