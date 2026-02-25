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
        Schema::create('variations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('product_id')->nullable();
            $table->float('price', 6)->default(0);
            $table->string('sku', 20)->nullable();
            $table->string('ean', 20)->nullable();
            $table->string('mpn', 20)->nullable();
            $table->string('upc', 20)->nullable();
            $table->string('isbn', 20)->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('minimal_quantity')->default(0);
            $table->unsignedInteger('low_stock_alert')->default(0);
            $table->dateTime('available_from')->nullable();
            $table->dateTime('available_to')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variations');
    }
};