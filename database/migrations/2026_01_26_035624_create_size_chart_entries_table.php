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
        Schema::create('size_chart_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('size_chart_id')->constrained();
            $table->foreignId('variant_id')->nullable();
            $table->foreignId('target_unit_id')->nullable();
            $table->string('converted_value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('size_chart_entries');
    }
};
