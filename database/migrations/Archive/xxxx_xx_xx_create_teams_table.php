<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamsTable extends Migration {
    public function up() {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // Add this line
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('cascade');
        });
    }

    public function down() {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropColumn('team_id');
        });

        Schema::dropIfExists('teams');
    }
}
