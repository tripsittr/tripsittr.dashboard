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

            Schema::table('bands', function (Blueprint $table) {
                $table->string('website')->nullable();
                $table->string('instagram')->nullable();
                $table->string('twitter')->nullable();
                $table->string('facebook')->nullable();
                $table->string('youtube')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('bands', function (Blueprint $table) {
            $table->dropColumn([
                'website',
                'instagram',
                'twitter',
                'facebook',
                'youtube',
                'email',
                'phone'
            ]);
        });
    }
};
