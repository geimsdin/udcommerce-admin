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
        Schema::create('tax_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tax_rule_group')->constrained('tax_rule_groups', 'id')->onDelete('cascade');
            $table->unsignedBigInteger('id_country')->nullable();
            $table->unsignedBigInteger('id_state')->nullable();
            $table->string('zipcode_from', 12)->nullable();
            $table->string('zipcode_to', 12)->nullable();
            $table->foreignId('id_tax')->constrained('taxes', 'id')->onDelete('cascade');
            $table->tinyInteger('behavior')->default(0); // 0 = combine, 1 = one, 2 = highest
            $table->string('description', 100)->nullable();
            $table->timestamps();
            
            $table->index(['id_tax_rule_group']);
            $table->index(['id_country', 'id_state']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_rules');
    }
};

