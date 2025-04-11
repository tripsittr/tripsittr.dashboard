<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();
            $table->string('facebook')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('website')->nullable();
            $table->string('type'); // Add this line
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('cascade'); // Add this line
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('song_file');
            $table->string('isrc')->nullable();
            $table->string('upc')->nullable();
            $table->string('genre')->nullable();
            $table->string('subgenre')->nullable();
            $table->string('artwork')->nullable();
            $table->date('release_date')->nullable();
            $table->string('status')->default('unreleased');
            $table->string('visibility')->default('private');
            $table->string('distribution_status')->default('pending');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('albums', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('release_date')->nullable();
            $table->foreignId('band_id')->constrained()->onDelete('cascade');
            $table->foreignId('artist_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('bands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('formation_date')->nullable();
            $table->text('description')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('banner_image')->nullable();
            $table->json('genre')->nullable();
            $table->string('website')->nullable();
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();
            $table->string('facebook')->nullable();
            $table->string('youtube')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->json('members')->nullable();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->required();
            $table->string('batch_number')->nullable();
            $table->string('barcode')->nullable();
            $table->string('name')->required();
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->decimal('cost', 8, 2)->nullable();
            $table->date('exp_date')->nullable();
            $table->decimal('length', 8, 2)->nullable();
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->string('dims_unit')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->string('weight_unit')->nullable();
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->string('material')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('provider')->nullable();
            $table->string('provider_website')->nullable();
            $table->integer('stock')->default(0);
            $table->integer('low_stock_threshold')->nullable();
            $table->string('image')->nullable();
            $table->foreignId('band_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // ...other tables...
    }

    public function down() {
        Schema::dropIfExists('songs');
        Schema::dropIfExists('albums');
        Schema::dropIfExists('bands');
        Schema::dropIfExists('merchandise');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('users');
        Schema::dropIfExists('teams');

        // ...other tables...
    }
};
