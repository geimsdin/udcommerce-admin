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
        Schema::create('product_image_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')->constrained('languages', 'id');
            $table->foreignId('product_image_id');
            $table->string('caption', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_image_languages');
    }
};
