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
        if (!Schema::hasTable('country_address_custom_fields')) {
            Schema::create('country_address_custom_fields', function (Blueprint $table) {
                $table->id();
                $table->string('country', 2)->index(); // ISO Code e.g., IT, US
                $table->string('name')->index(); // internal key, e.g., 'codice_fiscale'
                $table->string('label'); // UI label
                $table->string('type')->default('text'); // text, number, alphanumeric
                $table->boolean('is_required')->default(false);
                $table->integer('min_length')->nullable();
                $table->integer('max_length')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('country_address_custom_fields');
    }
};
