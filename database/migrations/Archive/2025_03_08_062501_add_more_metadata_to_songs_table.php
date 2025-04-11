<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('songs', function (Blueprint $table) {
            // Additional Audio Metadata
            $table->decimal('file_size', 8, 2)->nullable(); // MB
            $table->string('file_extension')->nullable();
            $table->string('bit_depth')->nullable();
            $table->decimal('compression_ratio', 5, 2)->nullable();
            $table->string('encoder')->nullable();
            $table->string('channel_mode')->nullable();
            $table->string('mode_extension')->nullable();
            $table->integer('audio_data_offset')->nullable();
            $table->integer('audio_data_length')->nullable();
            $table->string('mime_type')->nullable();

            // Track & Album Metadata
            $table->string('track_number')->nullable();
            $table->string('disc_number')->nullable();
            $table->string('album_title')->nullable();
            $table->integer('year')->nullable();
            $table->integer('bpm')->nullable();
            $table->string('mood')->nullable();
            $table->string('key_signature')->nullable();
            $table->string('publisher')->nullable();
            $table->string('copyright')->nullable();
            $table->text('composer_notes')->nullable();
            $table->string('genre_extended')->nullable();
            $table->string('language')->nullable();

            // Additional Metadata Tags
            $table->string('album_artist')->nullable();
            $table->date('original_release_date')->nullable();
            $table->text('comment')->nullable();
            $table->text('lyrics')->nullable();
            $table->string('file_owner')->nullable();
            $table->string('encoded_by')->nullable();
            $table->string('performer_info')->nullable();
            $table->string('conductor')->nullable();
            $table->string('remixer')->nullable();
            $table->string('mix_artist')->nullable();
            $table->string('dj_mixer')->nullable();
            $table->string('author')->nullable();
            $table->string('grouping')->nullable();
            $table->string('subtitle')->nullable();
        });
    }

    public function down(): void {
        Schema::table('songs', function (Blueprint $table) {
            $table->dropColumn([
                'file_size',
                'file_extension',
                'bit_depth',
                'compression_ratio',
                'encoder',
                'channel_mode',
                'mode_extension',
                'audio_data_offset',
                'audio_data_length',
                'mime_type',
                'track_number',
                'disc_number',
                'album_title',
                'year',
                'bpm',
                'mood',
                'key_signature',
                'publisher',
                'copyright',
                'composer_notes',
                'genre_extended',
                'language',
                'album_artist',
                'original_release_date',
                'comment',
                'lyrics',
                'file_owner',
                'encoded_by',
                'performer_info',
                'conductor',
                'remixer',
                'mix_artist',
                'dj_mixer',
                'author',
                'grouping',
                'subtitle',
            ]);
        });
    }
};
