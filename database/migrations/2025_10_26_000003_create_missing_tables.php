<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('team_id')->nullable()->index();
                $table->string('entity_type')->nullable();
                $table->unsignedBigInteger('entity_id')->nullable();
                $table->string('action')->nullable();
                $table->json('changes')->nullable();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('team_id')->nullable()->index();
                $table->string('name')->nullable();
                $table->string('email')->nullable()->index();
                $table->string('phone')->nullable();
                $table->string('company')->nullable();
                $table->string('address_line1')->nullable();
                $table->string('address_line2')->nullable();
                $table->string('city')->nullable();
                $table->string('region')->nullable();
                $table->string('postal_code')->nullable();
                $table->string('country')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('approvals')) {
            Schema::create('approvals', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('approvable_type');
                $table->unsignedBigInteger('approvable_id');
                $table->unsignedBigInteger('submitted_by')->nullable();
                $table->unsignedBigInteger('team_id')->nullable()->index();
                $table->string('status')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamp('submitted_at')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->text('notes')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('direct_message_threads')) {
            Schema::create('direct_message_threads', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('subject')->nullable();
                $table->unsignedBigInteger('team_id')->nullable()->index();
                $table->unsignedBigInteger('booking_request_id')->nullable()->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('catalog_items')) {
            Schema::create('catalog_items', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('team_id')->nullable()->index();
                $table->string('part_number')->nullable()->index();
                $table->string('reference_code')->nullable();
                $table->string('name')->nullable()->index();
                $table->text('description')->nullable();
                $table->string('material')->nullable()->index();
                $table->string('brand')->nullable()->index();
                $table->decimal('default_cost', 10, 2)->nullable();
                $table->decimal('default_price', 10, 2)->nullable();
                $table->integer('length')->nullable();
                $table->integer('width')->nullable();
                $table->integer('height')->nullable();
                $table->string('dims_unit')->nullable();
                $table->decimal('weight', 10, 2)->nullable();
                $table->string('weight_unit')->nullable();
                $table->integer('default_lead_time_days')->nullable();
                $table->text('notes')->nullable();
                $table->string('item_type')->nullable();
                $table->json('sizes')->nullable();
                $table->json('colors')->nullable();
                $table->string('format')->nullable();
                $table->integer('runtime_minutes')->nullable();
                $table->integer('warranty_months')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('team_id')->nullable()->index();
                $table->unsignedBigInteger('customer_id')->nullable()->index();
                $table->string('reference')->nullable()->index();
                $table->string('status')->nullable();
                $table->timestamp('placed_at')->nullable();
                $table->string('shipping_first_name')->nullable();
                $table->string('shipping_last_name')->nullable();
                $table->string('shipping_company')->nullable();
                $table->string('shipping_email')->nullable();
                $table->string('shipping_phone')->nullable();
                $table->string('shipping_address_line1')->nullable();
                $table->string('shipping_address_line2')->nullable();
                $table->string('shipping_city')->nullable();
                $table->string('shipping_region')->nullable();
                $table->string('shipping_postal_code')->nullable();
                $table->string('shipping_country')->nullable();
                $table->string('shipping_carrier')->nullable();
                $table->string('shipping_method')->nullable();
                $table->boolean('shipping_saturday_delivery')->default(false);
                $table->string('shipping_reference')->nullable();
                $table->string('shipping_reference_2')->nullable();
                $table->string('tracking_number')->nullable();
                $table->decimal('shipping_cost', 10, 2)->nullable();
                $table->decimal('subtotal', 10, 2)->nullable();
                $table->decimal('tax_total', 10, 2)->nullable();
                $table->decimal('total', 10, 2)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! Schema::hasTable('venue_events')) {
            Schema::create('venue_events', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('venue_id')->nullable()->index();
                $table->string('name')->nullable();
                $table->text('description')->nullable();
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->unsignedBigInteger('team_id')->nullable()->index();
                $table->timestamps();
            });
        }

        // Attempt to add foreign keys where the teams table exists
        if (Schema::hasTable('teams')) {
            $tables = ['activity_logs','customers','approvals','direct_message_threads','catalog_items','orders','venue_events'];
            foreach ($tables as $t) {
                if (! Schema::hasTable($t)) {
                    continue;
                }
                if (! Schema::hasColumn($t, 'team_id')) {
                    continue;
                }
                try {
                    Schema::table($t, function (Blueprint $table) use ($t) {
                        $table->foreign('team_id')->references('id')->on('teams')->nullOnDelete();
                    });
                } catch (\Throwable $e) {
                    // ignore FK add errors
                }
            }
        }
    }

    public function down(): void
    {
        foreach (['activity_logs','customers','approvals','direct_message_threads','catalog_items','orders','venue_events'] as $t) {
            if (Schema::hasTable($t)) {
                Schema::dropIfExists($t);
            }
        }
    }
};
