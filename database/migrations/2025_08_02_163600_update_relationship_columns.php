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
        // Add relationship columns to songs
        Schema::table('songs', function (Blueprint $table) {
            if (!Schema::hasColumn('songs', 'album_id')) {
                $table->unsignedBigInteger('album_id')->nullable()->index();
            }
        });

        // Add relationship columns to albums
        Schema::table('albums', function (Blueprint $table) {
            if (!Schema::hasColumn('albums', 'band_id')) {
                $table->unsignedBigInteger('band_id')->nullable()->index();
            }
            if (!Schema::hasColumn('albums', 'artist_id')) {
                $table->unsignedBigInteger('artist_id')->nullable()->index();
            }
        });

        // Add relationship columns to inventory_items
        Schema::table('inventory_items', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_items', 'band_id')) {
                $table->unsignedBigInteger('band_id')->nullable()->index();
            }
            if (!Schema::hasColumn('inventory_items', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->index();
            }
            if (!Schema::hasColumn('inventory_items', 'tenant_id')) {
                $table->unsignedBigInteger('tenant_id')->nullable()->index();
            }
        });

        // Add relationship columns to events
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'author_id')) {
                $table->unsignedBigInteger('author_id')->nullable()->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->dropColumn('album_id');
        });
        Schema::table('albums', function (Blueprint $table) {
            $table->dropColumn(['band_id', 'artist_id']);
        });
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropColumn(['band_id', 'user_id', 'tenant_id']);
        });
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('author_id');
        });
    }
};
