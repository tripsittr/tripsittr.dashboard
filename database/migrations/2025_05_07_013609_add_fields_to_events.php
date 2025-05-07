<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('venue')->nullable();
            $table->string('priority')->nullable();

            $table->text('notes')->nullable();

            $table->string('status')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_website')->nullable();
            $table->string('contact_address')->nullable();
            $table->string('contact_address2')->nullable();
            $table->string('contact_city')->nullable();
            $table->string('contact_state')->nullable();
            $table->integer('contact_zip')->nullable();
            $table->string('contact_country')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            //
        });
    }
};
