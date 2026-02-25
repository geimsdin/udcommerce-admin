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
        Schema::create('carriers', function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->default(1);
            $table->string('icon', 40);
            $table->decimal('price')->default(0);
            $table->integer('position')->default(1);
            $table->softDeletesDatetime();
            $table->timestamps();
        });

        Schema::create('carrier_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')->constrained('languages', 'id');
            $table->foreignId('carrier_id')->constrained('carriers', 'id');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carriers');
        Schema::dropIfExists('carrier_languages');
    }
};
