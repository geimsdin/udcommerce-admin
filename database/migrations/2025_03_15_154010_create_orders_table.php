<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('reference', 10);
            $table->unsignedInteger('carrier_id')->default(0);
            $table->unsignedBigInteger('address_id')->default(0);
            $table->unsignedInteger('currency_id');
            $table->unsignedInteger('season_id');
            $table->text('note')->nullable();
            $table->text('guest_id')->nullable();
            $table->unsignedInteger('last_status_id')->default(0);
            $table->string('last_status_name', 50);

            $table->enum('discount_type', ['percent', 'amount'])->default('percent');
            $table->float('discount')->default(0);
            $table->unsignedBigInteger('coupon_id')->nullable();

            $table->string('payment_method', 30)->nullable();
            $table->text('payment_info')->nullable();

            $table->boolean('returned')->default(false);
            $table->text('return_note')->nullable();
            $table->float('return_amount')->default(0);

            $table->softDeletesDatetime();
            $table->timestamps(); // created_at and updated_at TIMESTAMP NULL

            // Indexes
            $table->index('client_id');
            $table->index('address_id');
            $table->index('currency_id');
            $table->index('guest_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
