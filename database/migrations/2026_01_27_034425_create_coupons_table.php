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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->nullable();
            $table->text('description')->nullable();
            $table->enum('type', ['fixed', 'percentage'])->default('fixed');
            $table->decimal('value', 10, 2)->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->decimal('minimum_spend', 10, 2)->nullable();
            $table->decimal('maximum_spend', 10, 2)->nullable();
            $table->unsignedInteger('limit_use_by_user')->nullable();
            $table->unsignedInteger('limit_use_by_coupon')->nullable();
            $table->json('include_brands')->nullable();
            $table->json('exclude_brands')->nullable();
            $table->json('include_categories')->nullable();
            $table->json('exclude_categories')->nullable();
            $table->json('include_products')->nullable();
            $table->json('exclude_products')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
