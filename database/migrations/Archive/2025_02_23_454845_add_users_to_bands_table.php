<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::table('bands', function (Blueprint $table) {
            $table->text('members')->nullable();
            $table->removeColumn('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table('bands', function (Blueprint $table) {
            $table->dropColumn('members');
        });
    }
};
