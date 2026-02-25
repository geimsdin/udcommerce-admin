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
        Schema::create('order_status_languages', function (Blueprint $table) {
            $table->unsignedBigInteger('order_status_id');
            $table->unsignedBigInteger('language_id');
            $table->string('name', 50);

            $table->index(['order_status_id', 'language_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_status_languages');
    }
};
