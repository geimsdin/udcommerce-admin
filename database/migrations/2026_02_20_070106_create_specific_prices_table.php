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
        Schema::create('specific_prices', function (Blueprint $table) {
            $table->id();
            $table->integer('id_product');
            $table->integer('id_currency');
            $table->integer('id_client_type');
            $table->integer('id_customer');
            $table->decimal('price');
            $table->integer('from_quantity');
            $table->decimal('reduction');
            $table->boolean('reduction_tax')->default(false);
            $table->enum('reduction_type', ['amount', 'percentage']);
            $table->dateTime('from')->nullable();
            $table->dateTime('to')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specific_prices');
    }
};
