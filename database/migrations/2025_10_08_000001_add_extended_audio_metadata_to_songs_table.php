<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            // Choose safe existing reference columns for ordering; fall back if missing.
            $afterColumn = Schema::hasColumn('songs', 'artwork') ? 'artwork' : 'song_file';

            // Ensure base technical columns exist (were present in an earlier archived migration)
            $baseCols = [
                'duration' => fn() => $table->decimal('duration', 10, 2)->nullable()->after($afterColumn),
                'bitrate' => fn() => $table->integer('bitrate')->nullable()->after('duration'),
                'sample_rate' => fn() => $table->integer('sample_rate')->nullable()->after('bitrate'),
                'codec' => fn() => $table->string('codec', 64)->nullable()->after('sample_rate'),
                'format' => fn() => $table->string('format', 32)->nullable()->after('codec'),
                'channels' => fn() => $table->unsignedTinyInteger('channels')->nullable()->after('format'),
                'file_size' => fn() => $table->decimal('file_size', 12, 2)->nullable()->after('channels'),
                'mime_type' => fn() => $table->string('mime_type', 64)->nullable()->after('file_size'),
                'track_number' => fn() => $table->unsignedSmallInteger('track_number')->nullable()->after('mime_type'),
                'disc_number' => fn() => $table->unsignedSmallInteger('disc_number')->nullable()->after('track_number'),
            ];
            foreach ($baseCols as $col => $creator) {
                if (!Schema::hasColumn('songs', $col)) {
                    $creator();
                }
            }
            if (!Schema::hasColumn('songs', 'raw_metadata')) {
                $table->json('raw_metadata')->nullable()->after($afterColumn);
            }
            if (!Schema::hasColumn('songs', 'bitrate_mode')) {
                $ref = Schema::hasColumn('songs', 'bitrate') ? 'bitrate' : 'duration';
                $table->string('bitrate_mode', 16)->nullable()->after($ref);
            }
            if (!Schema::hasColumn('songs', 'replay_gain_track')) {
                $table->decimal('replay_gain_track', 6, 2)->nullable()->after('bitrate_mode');
            }
            if (!Schema::hasColumn('songs', 'replay_gain_album')) {
                $table->decimal('replay_gain_album', 6, 2)->nullable()->after('replay_gain_track');
            }
            if (!Schema::hasColumn('songs', 'track_total')) {
                $table->unsignedSmallInteger('track_total')->nullable()->after('track_number');
            }
            if (!Schema::hasColumn('songs', 'disc_total')) {
                $table->unsignedSmallInteger('disc_total')->nullable()->after('disc_number');
            }
            if (!Schema::hasColumn('songs', 'md5_file')) {
                $table->char('md5_file', 32)->nullable()->after('raw_metadata');
            }
        });
    }

    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $drops = [
                'raw_metadata','bitrate_mode','replay_gain_track','replay_gain_album','track_total','disc_total','md5_file'
            ];
            foreach ($drops as $col) {
                if (Schema::hasColumn('songs', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
