<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            if(! Schema::hasColumn('teams','plan_slug')) {
                $table->string('plan_slug')->default(config('plans.default_plan'));
            }
            if(! Schema::hasColumn('teams','plan_started_at')) {
                $table->timestamp('plan_started_at')->nullable();
            }
            if(! Schema::hasColumn('teams','plan_renews_at')) {
                $table->timestamp('plan_renews_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            if(Schema::hasColumn('teams','plan_slug')) $table->dropColumn('plan_slug');
            if(Schema::hasColumn('teams','plan_started_at')) $table->dropColumn('plan_started_at');
            if(Schema::hasColumn('teams','plan_renews_at')) $table->dropColumn('plan_renews_at');
        });
    }
};