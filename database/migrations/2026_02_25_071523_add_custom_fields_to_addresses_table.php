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
        Schema::table('addresses', function (Blueprint $table) {
            if (!Schema::hasColumn('addresses', 'custom_fields')) {
                $table->json('custom_fields')->nullable()->after('phone');
            }
        });
        Schema::table('addresses', function (Blueprint $table) {
            if (!Schema::hasColumn('addresses', 'destination_name')) {
                $table->string('destination_name')->nullable()->after('client_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            if (Schema::hasColumn('addresses', 'custom_fields')) {
                $table->dropColumn('custom_fields');
            }
        });
        Schema::table('addresses', function (Blueprint $table) {
            if (Schema::hasColumn('addresses', 'destination_name')) {
                $table->dropColumn('destination_name');
            }
        });
    }
};
