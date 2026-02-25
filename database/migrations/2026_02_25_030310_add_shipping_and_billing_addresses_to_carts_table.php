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
        Schema::table('carts', function (Blueprint $table) {
            $table->unsignedBigInteger('shipping_address_id')->nullable()->after('carrier_id');
            $table->unsignedBigInteger('billing_address_id')->nullable()->after('shipping_address_id');
        });

        // Copy existing address_id to shipping_address_id and billing_address_id
        \Illuminate\Support\Facades\DB::statement('UPDATE carts SET shipping_address_id = address_id, billing_address_id = address_id WHERE address_id IS NOT NULL');

        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn('address_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            // Re-add address_id
            $table->unsignedBigInteger('address_id')->default(0)->after('carrier_id');
        });

        // Copy shipping_address_id back to address_id (assuming it exists)
        \Illuminate\Support\Facades\DB::statement('UPDATE carts SET address_id = shipping_address_id WHERE shipping_address_id IS NOT NULL');

        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn('shipping_address_id');
            $table->dropColumn('billing_address_id');
        });
    }
};
