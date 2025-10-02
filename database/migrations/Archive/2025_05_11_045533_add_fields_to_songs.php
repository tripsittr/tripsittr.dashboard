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
        Schema::table('songs', function (Blueprint $table) {
            $table->json('primary_artists')->nullable(); // JSON array for primary artists
            $table->json('featured_artists')->nullable(); // JSON array for featured artists
            $table->json('producers')->nullable(); // JSON array for producers
            $table->json('composers')->nullable(); // JSON array for composers
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            //
        });
    }
};
