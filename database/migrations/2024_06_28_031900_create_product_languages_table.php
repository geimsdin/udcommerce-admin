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
        Schema::create('product_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')->constrained('languages', 'id');
            $table->foreignId('product_id')->constrained('products', 'id');
            $table->string('name', 150);
            $table->text('description_long')->nullable();
            $table->text('description_short')->nullable();
            $table->string('link_rewrite', 128);
            $table->string('meta_title', 128)->nullable();
            $table->string('meta_description', 512)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_languages');
    }
};
