<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // If legacy singular table exists and plural doesn't, rename.
        if (Schema::hasTable('action') && ! Schema::hasTable('actions')) {
            Schema::rename('action', 'actions');
        }

        // If neither exists, create fresh plural table.
        if (! Schema::hasTable('actions')) {
            Schema::create('actions', function (Blueprint $table) {
                $table->id();
                $table->string('action_title');
                $table->string('action_type')->index();
                $table->timestamps();
            });
        } else {
            // Ensure required columns exist (idempotent hardening)
            Schema::table('actions', function (Blueprint $table) {
                if (! Schema::hasColumn('actions', 'action_title')) {
                    $table->string('action_title')->after('id');
                }
                if (! Schema::hasColumn('actions', 'action_type')) {
                    $table->string('action_type')->after('action_title');
                }
            });
        }

        // Seed baseline actions if absent.
        $seedActions = [
            ['action_title' => 'Create Album', 'action_type' => 'create_album'],
            ['action_title' => 'Update Album', 'action_type' => 'update_album'],
            ['action_title' => 'Delete Album', 'action_type' => 'delete_album'],
        ];
        foreach ($seedActions as $row) {
            $exists = DB::table('actions')->where('action_type', $row['action_type'])->exists();
            if (! $exists) {
                DB::table('actions')->insert(array_merge($row, [
                    'created_at' => now(), 'updated_at' => now(),
                ]));
            }
        }
    }

    public function down(): void
    {
        // We only revert if table originally came from this normalization (i.e., plural)
        if (Schema::hasTable('actions')) {
            Schema::drop('actions');
        }
    }
};
