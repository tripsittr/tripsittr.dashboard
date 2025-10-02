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
        Schema::create('songs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('song_file');
            $table->string('isrc')->nullable();
            $table->string('upc')->nullable();
            $table->string('genre')->nullable();
            $table->string('subgenre')->nullable();
            $table->string('artwork')->nullable();
            $table->date('release_date')->nullable();
            $table->string('status')->default('unreleased');
            $table->string('visibility')->default('private');
            $table->string('distribution_status')->default('pending');
            $table->unsignedBigInteger('user_id')->index('songs_user_id_foreign');
            $table->unsignedBigInteger('team_id')->nullable()->index('songs_team_id_foreign');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
