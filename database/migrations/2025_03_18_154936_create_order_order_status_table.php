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
        Schema::create('order_order_status', function (Blueprint $table) {
            $table->unsignedBigInteger('order_status_id');
            $table->unsignedBigInteger('order_id');
            $table->timestamps();

            $table->index(['order_status_id', 'order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_order_status');
    }
};
