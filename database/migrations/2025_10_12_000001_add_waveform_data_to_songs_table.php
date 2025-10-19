<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            if (!Schema::hasColumn('songs', 'waveform_data')) {
                // Store generated waveform amplitudes as JSON array of floats
                $after = Schema::hasColumn('songs', 'raw_metadata') ? 'raw_metadata' : null;
                if ($after) {
                    $table->json('waveform_data')->nullable()->after($after);
                } else {
                    $table->json('waveform_data')->nullable();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            if (Schema::hasColumn('songs', 'waveform_data')) {
                $table->dropColumn('waveform_data');
            }
        });
    }
};
