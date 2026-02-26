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
        Schema::create('tax_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tax_id')->constrained('taxes', 'id')->onDelete('cascade');
            $table->foreignId('language_id')->constrained('languages', 'id')->onDelete('cascade');
            $table->string('name', 32);
            $table->timestamps();
            
            $table->unique(['tax_id', 'language_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_languages');
    }
};

