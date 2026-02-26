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
        // have to update this migration
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('reference', 10);
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedInteger('carrier_id')->default(0);
            $table->unsignedBigInteger('address_id')->default(0);
            $table->unsignedInteger('season_id')->default(0);
            $table->unsignedInteger('currency_id');
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->text('note')->nullable();
            $table->text('guest_id')->nullable();
            $table->string('secure_key', 64);
            $table->timestamps();

            $table->index(['order_id', 'client_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
