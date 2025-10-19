<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            if(! Schema::hasColumn('invitations','revoked_at')) {
                $table->timestamp('revoked_at')->nullable()->after('accepted_at');
            }
            if(! Schema::hasColumn('invitations','expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('revoked_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            if(Schema::hasColumn('invitations','revoked_at')) $table->dropColumn('revoked_at');
            if(Schema::hasColumn('invitations','expires_at')) $table->dropColumn('expires_at');
        });
    }
};