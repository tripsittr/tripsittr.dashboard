<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('instagram')->nullable()->after('phone');
            $table->string('twitter')->nullable()->after('instagram');
            $table->string('facebook')->nullable()->after('twitter');
            $table->string('linkedin')->nullable()->after('facebook');
            $table->string('website')->nullable()->after('linkedin');
        });
    }

    public function down() {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'instagram', 'twitter', 'facebook', 'linkedin', 'website']);
        });
    }
};
