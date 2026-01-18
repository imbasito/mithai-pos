<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds indexes to improve POS product queries performance.
     * - status: Used in scopeActive() to filter active products
     * - quantity: Used in scopeStocked() to filter in-stock products
     * - name: Used in product search LIKE queries
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index('status', 'idx_products_status');
            $table->index('quantity', 'idx_products_quantity');
            $table->index('name', 'idx_products_name');
            // Composite index for the common POS query pattern
            $table->index(['status', 'quantity'], 'idx_products_active_stocked');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_status');
            $table->dropIndex('idx_products_quantity');
            $table->dropIndex('idx_products_name');
            $table->dropIndex('idx_products_active_stocked');
        });
    }
};
