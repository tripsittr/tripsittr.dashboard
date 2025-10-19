<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('subscriptions')) {
            return; // already created
        }
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            // Support either user-based or team-based billing (team preferred)
            $table->foreignId('team_id')->nullable()->index();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('type');
            $table->string('stripe_id')->unique();
            $table->string('stripe_status')->index();
            $table->string('stripe_price')->nullable();
            $table->integer('quantity')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
            $table->index(['team_id','stripe_status']);
            $table->index(['user_id','stripe_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
