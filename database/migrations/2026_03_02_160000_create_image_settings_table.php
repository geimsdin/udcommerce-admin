<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('image_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64)->unique();
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
            $table->boolean('products')->default(false);
            $table->boolean('categories')->default(false);
            $table->boolean('brands')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_settings');
    }
};
