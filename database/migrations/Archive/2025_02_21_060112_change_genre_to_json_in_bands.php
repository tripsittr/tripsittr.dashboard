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
            $table->json('genre')->nullable()->change(); // ✅ Store genres as JSON
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('bands', function (Blueprint $table) {
            $table->string('genre')->nullable()->change(); // Revert to a single string
        });
    }
};
