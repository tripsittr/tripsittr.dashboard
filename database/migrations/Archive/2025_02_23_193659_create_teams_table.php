<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::table('bands', function (Blueprint $table) {
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('cascade');
        });

        Schema::table('merchandise', function (Blueprint $table) {
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('cascade');
        });

        Schema::table('songs', function (Blueprint $table) {
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('cascade');
        });

        Schema::table('albums', function (Blueprint $table) {
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('teams');
        Schema::table('bands', fn(Blueprint $table) => $table->dropColumn('team_id'));
        Schema::table('merchandise', fn(Blueprint $table) => $table->dropColumn('team_id'));
        Schema::table('songs', fn(Blueprint $table) => $table->dropColumn('team_id'));
        Schema::table('albums', fn(Blueprint $table) => $table->dropColumn('team_id'));
    }
};
