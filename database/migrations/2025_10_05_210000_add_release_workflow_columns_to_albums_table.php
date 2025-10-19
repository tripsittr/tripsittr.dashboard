<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('albums', function (Blueprint $table) {
            if (!Schema::hasColumn('albums', 'status')) {
                $table->string('status')->default('draft')->after('title'); // draft, in_review, approved, scheduled, released, rejected
            }
            if (!Schema::hasColumn('albums', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('team_id')->index();
            }
            if (!Schema::hasColumn('albums', 'submitted_for_review_at')) {
                $table->timestamp('submitted_for_review_at')->nullable();
            }
            if (!Schema::hasColumn('albums', 'approved_at')) {
                $table->timestamp('approved_at')->nullable();
            }
            if (!Schema::hasColumn('albums', 'released_at')) {
                $table->timestamp('released_at')->nullable();
            }
            if (!Schema::hasColumn('albums', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->index();
            }
            if (!Schema::hasColumn('albums', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('albums', function (Blueprint $table) {
            foreach (['status','user_id','submitted_for_review_at','approved_at','released_at','approved_by','rejection_reason'] as $col) {
                if (Schema::hasColumn('albums', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
