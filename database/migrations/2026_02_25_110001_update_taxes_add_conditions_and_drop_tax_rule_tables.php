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
        // Ensure foreign key checks don't block table drops
        Schema::disableForeignKeyConstraints();

        // Add condition fields directly on taxes
        Schema::table('taxes', function (Blueprint $table) {
            if (!Schema::hasColumn('taxes', 'id_country')) {
                $table->unsignedBigInteger('id_country')->nullable()->after('active');
            }

            if (!Schema::hasColumn('taxes', 'id_state')) {
                $table->unsignedBigInteger('id_state')->nullable()->after('id_country');
            }

            if (!Schema::hasColumn('taxes', 'zipcode_from')) {
                $table->string('zipcode_from', 12)->nullable()->after('id_state');
            }

            if (!Schema::hasColumn('taxes', 'zipcode_to')) {
                $table->string('zipcode_to', 12)->nullable()->after('zipcode_from');
            }
        });

        // Drop old rule/group tables – all conditions now live on taxes
        if (Schema::hasTable('tax_rules')) {
            Schema::dropIfExists('tax_rules');
        }

        if (Schema::hasTable('tax_rule_groups')) {
            Schema::dropIfExists('tax_rule_groups');
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove condition fields from taxes
        Schema::table('taxes', function (Blueprint $table) {
            if (Schema::hasColumn('taxes', 'zipcode_to')) {
                $table->dropColumn('zipcode_to');
            }
            if (Schema::hasColumn('taxes', 'zipcode_from')) {
                $table->dropColumn('zipcode_from');
            }
            if (Schema::hasColumn('taxes', 'id_state')) {
                $table->dropColumn('id_state');
            }
            if (Schema::hasColumn('taxes', 'id_country')) {
                $table->dropColumn('id_country');
            }
        });

        // Recreate minimal tax_rule_groups and tax_rules tables
        if (!Schema::hasTable('tax_rule_groups')) {
            Schema::create('tax_rule_groups', function (Blueprint $table) {
                $table->id();
                $table->string('name', 50);
                $table->boolean('active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('tax_rules')) {
            Schema::create('tax_rules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('id_tax_rule_group')->constrained('tax_rule_groups', 'id')->onDelete('cascade');
                $table->unsignedBigInteger('id_country')->nullable();
                $table->unsignedBigInteger('id_state')->nullable();
                $table->string('zipcode_from', 12)->nullable();
                $table->string('zipcode_to', 12)->nullable();
                $table->foreignId('id_tax')->constrained('taxes', 'id')->onDelete('cascade');
                $table->tinyInteger('behavior')->default(0);
                $table->string('description', 100)->nullable();
                $table->timestamps();
            });
        }
    }
};

