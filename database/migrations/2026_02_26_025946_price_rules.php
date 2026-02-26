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
        Schema::create('price_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('brand_id')->nullable();
            $table->unsignedInteger('category_id')->nullable();
            $table->unsignedInteger('feature_id')->nullable();
            $table->unsignedInteger('client_group_id')->nullable();
            $table->decimal('from_price', 10, 2)->nullable();
            $table->decimal('to_price', 10, 2)->nullable();
            $table->unsignedInteger('from_quantity')->default(1);
            $table->enum('reduction_type', ['amount', 'percentage'])->default('percentage');
            $table->decimal('reduction_value', 10, 2)->default(0.00);
            $table->boolean('reduction_includes_tax')->default(false);
            $table->dateTime('from_date')->nullable();
            $table->dateTime('to_date')->nullable();
            $table->unsignedInteger('priority')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_rules');
    }
};
