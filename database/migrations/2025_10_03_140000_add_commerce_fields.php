<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Tax rate on teams
        Schema::table('teams', function(Blueprint $table){
            if(!Schema::hasColumn('teams','tax_rate')){
                $table->decimal('tax_rate',5,2)->default(0);
            }
        });
        // Reservation columns on inventory
        Schema::table('inventory_items', function(Blueprint $table){
            if(!Schema::hasColumn('inventory_items','reserved')){
                $table->unsignedInteger('reserved')->default(0)->after('stock');
            }
        });
        // Activity logs (lightweight)
        if(!Schema::hasTable('activity_logs')){
            Schema::create('activity_logs', function(Blueprint $table){
                $table->bigIncrements('id');
                $table->unsignedBigInteger('team_id')->nullable();
                $table->string('entity_type')->nullable();
                $table->unsignedBigInteger('entity_id')->nullable();
                $table->string('action');
                $table->json('changes')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();
                $table->index(['team_id','entity_type']);
            });
        }
    }
    public function down(): void {
        Schema::table('teams', function(Blueprint $table){
            if(Schema::hasColumn('teams','tax_rate')) $table->dropColumn('tax_rate');
        });
        Schema::table('inventory_items', function(Blueprint $table){
            if(Schema::hasColumn('inventory_items','reserved')) $table->dropColumn('reserved');
        });
        Schema::dropIfExists('activity_logs');
    }
};
