<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('addresses', function(Blueprint $table){
            $table->id();
            $table->unsignedBigInteger('team_id')->index();
            $table->string('label')->nullable();
            $table->string('line1');
            $table->string('line2')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->string('hash', 40)->index();
            $table->timestamps();
            $table->unique(['team_id','hash']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
