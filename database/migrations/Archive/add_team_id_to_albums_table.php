<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTeamIdToAlbumsTable extends Migration {
    public function up() {
        Schema::table('albums', function (Blueprint $table) {
            $table->unsignedBigInteger('team_id')->nullable()->after('release_date'); // Add 'team_id' column
        });
    }

    public function down() {
        Schema::table('albums', function (Blueprint $table) {
            $table->dropColumn('team_id');
        });
    }
}
