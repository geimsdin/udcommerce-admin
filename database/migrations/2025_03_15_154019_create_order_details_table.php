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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('variation_id')->nullable();
            $table->unsignedBigInteger('customization_id')->default(0);
            $table->unsignedInteger('language_id');
            $table->string('product_name');
            $table->integer('quantity')->default(0);
            $table->float('price')->default(0);

            $table->enum('discount_type', ['percent', 'amount'])->default('percent'); // PRODUCT LEVEL DISCOUNT
            $table->float('discount')->default(0); // PRODUCT LEVEL DISCOUNT
            $table->unsignedBigInteger('coupon_id')->nullable(); // PRODUCT LEVEL DISCOUNT

            $table->boolean('returned')->default(false);
            $table->text('return_note')->nullable();
            $table->float('return_amount')->default(0);

            $table->timestamps();

            // Set composite primary key
            $table->index(['id', 'order_id', 'product_id', 'variation_id', 'customization_id'], 'od_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
