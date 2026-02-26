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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('destination_name')->nullable();
            $table->string('company')->nullable();
            $table->string('vat_number')->nullable();
            $table->string('address1');
            $table->string('address2')->nullable();
            $table->string('postcode');
            $table->string('city');
            $table->string('province')->nullable();
            $table->string('country');
            $table->string('phone');
            $table->json('custom_fields')->nullable()
            $table->boolean('default')->default(false);
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
