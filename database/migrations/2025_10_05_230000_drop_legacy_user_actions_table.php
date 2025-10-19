<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('user_actions')) {
            Schema::drop('user_actions');
        }
    }

    public function down(): void
    {
        // Intentionally not recreating legacy table; this is a destructive cleanup.
    }
};
