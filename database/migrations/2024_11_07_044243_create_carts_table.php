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
        // have to update this migration
        Schema::create('carts', function (Blueprint $table) {
            $table->id(); // BIGINT(20) AUTO_INCREMENT PRIMARY KEY
            $table->unsignedBigInteger('order_id')->nullable(); // BIGINT(20) NULL
            $table->string('reference', 10);
            $table->unsignedBigInteger('client_id')->nullable(); // BIGINT(20) UNSIGNED
            $table->unsignedInteger('carrier_id')->default(0); // INT(10) UNSIGNED DEFAULT 0
            $table->unsignedBigInteger('address_id')->default(0); // INT(10) UNSIGNED
            $table->unsignedInteger('season_id')->default(0); // INT(10) UNSIGNED
            $table->unsignedInteger('currency_id'); // INT(10) UNSIGNED
            $table->text('note')->nullable(); // TEXT NULL
            $table->text('guest_id')->nullable(); // TEXT NULL
            $table->string('secure_key', 64); // VARCHAR(64) NULL
            $table->timestamps(); // created_at and updated_at TIMESTAMP NULL

            $table->index(['order_id', 'client_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
