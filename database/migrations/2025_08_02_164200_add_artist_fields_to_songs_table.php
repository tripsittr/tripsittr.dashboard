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
            if (!Schema::hasColumn('songs', 'primary_artists')) {
                $table->json('primary_artists')->nullable();
            }
            if (!Schema::hasColumn('songs', 'featured_artists')) {
                $table->json('featured_artists')->nullable();
            }
            if (!Schema::hasColumn('songs', 'producers')) {
                $table->json('producers')->nullable();
            }
            if (!Schema::hasColumn('songs', 'composers')) {
                $table->json('composers')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->dropColumn(['primary_artists', 'featured_artists', 'producers', 'composers']);
        });
    }
};
