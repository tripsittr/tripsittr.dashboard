<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('bands', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->date('formation_date')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('banner_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('bands', function (Blueprint $table) {
            $table->dropColumn(['genre', 'description', 'formation_date', 'profile_picture', 'banner_image']);
        });
    }
};
