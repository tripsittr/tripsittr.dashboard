<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        // Schema::table('roles', function (Blueprint $table) {
        //     $table->foreignId('team_id')->constrained();
        // });
        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->foreignId('team_id')->constrained();
        });
        Schema::table('model_has_permissions', function (Blueprint $table) {
            $table->foreignId('team_id')->constrained();
        });
        // Schema::table('permissions', function (Blueprint $table) {
        //     $table->foreignId('team_id')->constrained();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('spatie_roles_tables', function (Blueprint $table) {
            //
        });
    }
};
