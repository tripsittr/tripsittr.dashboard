<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('song_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('external_id')->nullable();
            $table->unsignedBigInteger('streams')->default(0);
            $table->decimal('streams_pct', 8, 4)->nullable();
            $table->bigInteger('streams_change')->nullable();
            $table->decimal('streams_change_pct', 8, 4)->nullable();
            $table->unsignedBigInteger('downloads')->default(0);
            $table->decimal('downloads_pct', 8, 4)->nullable();
            $table->bigInteger('downloads_change')->nullable();
            $table->decimal('downloads_change_pct', 8, 4)->nullable();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->timestamp('imported_at')->nullable();
            $table->timestamps();
            $table->index(['team_id', 'external_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('song_analytics');
    }
};
