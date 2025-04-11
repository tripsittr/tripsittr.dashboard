<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('songs', function (Blueprint $table) {
            $table->id();

            // 🎵 Basic Track Details
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('song_file')->nullable();
            $table->json('waveform_data')->nullable();
            $table->string('genre')->nullable();
            $table->string('subgenre')->nullable();

            // 🎚 Audio Metadata (Auto-filled)
            $table->decimal('duration', 8, 2)->nullable();
            $table->integer('bitrate')->nullable();
            $table->integer('sample_rate')->nullable();
            $table->string('codec')->nullable();
            $table->string('format')->nullable();
            $table->integer('channels')->nullable();

            // 👥 Credits (JSON for flexibility)
            $table->json('primary_artists')->nullable(); // List of primary artists
            $table->json('featured_artists')->nullable(); // List of featured artists
            $table->json('producers')->nullable(); // List of producers
            $table->json('composers')->nullable(); // List of composers
            $table->json('current_owners')->nullable(); // List of current owners (labels, individuals, etc.)

            // 📅 Release & Label Info
            $table->string('isrc')->nullable();
            $table->string('upc')->nullable();
            $table->string('label_name')->nullable();
            $table->string('release_date')->nullable();
            $table->enum('status', ['unreleased', 'scheduled', 'released'])->default('unreleased');
            $table->enum('visibility', ['public', 'private', 'unlisted'])->default('private'); // Now defaults to private
            $table->enum('distribution_status', ['pending', 'approved', 'rejected'])->default('pending');

            // 🏛 Licensing & Ownership
            $table->string('copyright_notice')->nullable();
            $table->enum('performance_rights_org', [
                '(USA) ASCAP',
                '(USA) BMI',
                '(USA) SESAC',
                '(Canada) SOCAN',
                '(UK) PRS for Music',
                '(Japan) JASRAC',
                '(Germany) GEMA',
                '(France) SACEM',
                '(Italy) SIAE',
                '(Netherlands) BUMA/STEMRA',
                '(Norway) TONO',
                '(Denmark) KODA',
                '(Sweden) STIM',
                '(Poland) ZAIKS',
                '(Belgium) SABAM',
                '(Finland) TEOSTO',
                '(Portugal) SPA',
                '(Serbia) SOKOJ',
                '(Ireland) IMRO',
                '(Singapore) COMPASS',
                '(Australia/NZ) APRA AMCOS',
                '(Bulgaria) MUSICAUTOR',
                '(Brazil) ECAD',
                '(South Africa) SAMRO',
                '(Trinidad & Tobago) COTT',
                '(Jamaica) JAMMS',
                '(China) MCSC',
                '(Malaysia) MACP',
                '(Taiwan) MCT',
                '(Colombia) SAYCO',
                '(USA) HARRY FOX'
            ])->nullable();
            $table->enum('license_type', ['exclusive', 'non-exclusive', 'creative-commons'])->nullable();

            // 📂 Artwork
            $table->string('artwork')->nullable();

            // 🔄 Polymorphic Relationship for Ownership (Team or Band)
            $table->morphs('ownable');

            // 🎵 Album Relationship (If it's part of an album)
            $table->foreignId('album_id')->nullable()->constrained()->onDelete('cascade');

            // 🕒 Timestamps
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::table('songs', function (Blueprint $table) {
            $table->dropColumn(['duration', 'bitrate', 'codec', 'sample_rate', 'channels']);
        });
    }
};
