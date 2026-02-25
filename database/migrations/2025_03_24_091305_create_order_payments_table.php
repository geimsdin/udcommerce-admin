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
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id');
            $table->foreignId('payment_method_id');
            $table->float('amount');
            $table->string('reference')->nullable();
            $table->string('stripe_session_id')->nullable();
            $table->string('stripe_charge_id')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'payment_method_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
