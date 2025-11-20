<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('social_accounts', function (Blueprint $table) {
            // store the Facebook Page id that owns the Instagram Business account
            $table->string('facebook_page_id')->nullable()->after('provider_id');
            // store the Instagram Business Account id (if available)
            $table->string('instagram_business_account_id')->nullable()->after('facebook_page_id');
        });
    }

    public function down(): void
    {
        Schema::table('social_accounts', function (Blueprint $table) {
            $table->dropColumn(['facebook_page_id', 'instagram_business_account_id']);
        });
    }
};
