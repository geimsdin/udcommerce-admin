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
        Schema::create('product_category_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')->constrained('languages', 'id');
            $table->foreignId('product_category_id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['language_id', 'product_category_id'], 'lang_prod');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_category_languages');
    }
};
