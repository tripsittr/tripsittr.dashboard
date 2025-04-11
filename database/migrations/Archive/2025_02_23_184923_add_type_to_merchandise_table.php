<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('merchandise', function (Blueprint $table) {
            $table->string('type')->after('image')->default('general');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('merchandise', function (Blueprint $table) {
            //
        });
    }
};
