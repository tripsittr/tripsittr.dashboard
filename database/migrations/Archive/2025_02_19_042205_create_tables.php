<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {

        // Schema::create('bands', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name');
        //     $table->string('genre')->nullable();
        //     $table->timestamps();
        // });

        // Schema::create('merch', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name');
        //     $table->decimal('price', 8, 2);
        //     $table->text('description')->nullable();
        //     $table->morphs('owner'); // Links to User or Band
        //     $table->timestamps();
        // });

        // Schema::create('albums', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('title');
        //     $table->date('release_date')->nullable();
        //     $table->foreignId('band_id')->nullable()->constrained()->onDelete('cascade');
        //     $table->foreignId('artist_id')->nullable()->constrained('users')->onDelete('cascade');
        //     $table->timestamps();
        // });

        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->string('song_name')->nullable();
            $table->string('song_file_name')->nullable();
            $table->string('song_file')->nullable();
            $table->string('genre')->nullable();
            $table->string('subgenre')->nullable();

            // Credits
            $table->string('primary_artist_1')->nullable();
            $table->string('primary_artist_2')->nullable();
            $table->string('primary_artist_3')->nullable();
            $table->string('primary_artist_4')->nullable();
            $table->string('featured_artist_1')->nullable();
            $table->string('featured_artist_2')->nullable();
            $table->string('featured_artist_3')->nullable();
            $table->string('featured_artist_4')->nullable();
            $table->string('producer_1')->nullable();
            $table->string('producer_2')->nullable();
            $table->string('producer_3')->nullable();
            $table->string('producer_4')->nullable();
            $table->string('original_composer_1')->nullable();
            $table->string('original_composer_2')->nullable();
            $table->string('original_composer_3')->nullable();
            $table->string('original_composer_4')->nullable();
            $table->string('current_owner')->nullable();

            // Release Info
            $table->string('preview_start_time')->nullable();
            $table->string('release_date')->nullable();
            $table->string('label_name')->nullable();
            $table->string('composition_owner')->nullable();
            $table->string('master_recording_owner')->nullable();
            $table->string('year_of_composition')->nullable();
            $table->string('year_of_recording')->nullable();
            $table->string('artwork_file_name')->nullable();
            $table->string('artwork')->nullable();
            $table->string('status')->nullable();
            $table->string('team_name')->nullable();

            $table->foreignId('album_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Schema::create('teams', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name');
        //     $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
        //     $table->timestamps();
        // });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('type', ['admin', 'artist', 'manager', 'label'])->default('artist')->change();
            // $table->foreignId('band_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('teams');
        Schema::dropIfExists('songs');
        Schema::dropIfExists('albums');
        Schema::dropIfExists('merch');
        Schema::dropIfExists('bands');
    }
};
