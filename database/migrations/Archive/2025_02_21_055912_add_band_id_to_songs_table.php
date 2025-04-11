<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('songs', function (Blueprint $table) {
            $table->foreignId('band_id')->nullable()->constrained('bands')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('songs', function (Blueprint $table) {
            $table->dropForeign(['band_id']);
            $table->dropColumn('band_id');
        });
    }
};
