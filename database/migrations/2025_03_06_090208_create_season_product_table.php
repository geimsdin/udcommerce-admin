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
        Schema::create('season_product', function (Blueprint $table) {
            $table->unsignedBigInteger('season_id');
            $table->unsignedBigInteger('product_id');
            $table->float('price')->default(0);
            $table->index(['season_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('season_product');
    }
};
