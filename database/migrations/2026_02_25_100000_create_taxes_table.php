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
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->decimal('rate', 10, 6)->default(0); // e.g., 20.000000 for 20%
            $table->unsignedBigInteger('id_country')->nullable();
            $table->unsignedBigInteger('id_state')->nullable();
            $table->string('zipcode_from', 12)->nullable();
            $table->string('zipcode_to', 12)->nullable();
            $table->string('behavior', 12)->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};

