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
        Schema::create('cart_details', function (Blueprint $table) {
            $table->unsignedBigInteger('cart_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger( 'variation_id');
            $table->unsignedInteger( 'language_id');
            $table->string( 'product_name');
            $table->integer('quantity')->default(0);
            $table->string('note', 250)->nullable();
            $table->timestamps();

            // Set composite primary key
            $table->primary(['cart_id', 'product_id', 'variation_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_details');
    }
};
