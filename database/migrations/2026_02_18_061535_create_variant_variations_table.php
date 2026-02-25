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
        Schema::create('variant_variations', function (Blueprint $table) {
            $table->foreignId('variation_id')->constrained('variations')->cascadeOnDelete();
            $table->foreignId('variant_id')->constrained('variants')->cascadeOnDelete();

            $table->primary(['variation_id', 'variant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variant_variations');
    }
};
