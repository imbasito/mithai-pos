<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Extra performance indexes for heavy data handling
     */
    public function up(): void
    {
        // Speed up order details lookups
        Schema::table('order_products', function (Blueprint $table) {
            if (!$this->hasIndex('order_products', 'idx_op_order_product')) {
                $table->index(['order_id', 'product_id'], 'idx_op_order_product');
            }
        });

        // Speed up customer lookups by phone
        Schema::table('customers', function (Blueprint $table) {
            if (!$this->hasIndex('customers', 'idx_customers_phone')) {
                $table->index('phone', 'idx_customers_phone');
            }
        });

        // Speed up purchase items lookups
        Schema::table('purchase_items', function (Blueprint $table) {
            if (!$this->hasIndex('purchase_items', 'idx_pi_purchase_product')) {
                $table->index(['purchase_id', 'product_id'], 'idx_pi_purchase_product');
            }
        });
        
        // Ensure SKU is indexed for fast scanning (if missing)
        Schema::table('products', function (Blueprint $table) {
            if (!$this->hasIndex('products', 'idx_products_sku')) {
                $table->index('sku', 'idx_products_sku');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropIndex('idx_op_order_product');
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('idx_customers_phone');
        });
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropIndex('idx_pi_purchase_product');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_sku');
        });
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        $indexes = Schema::getIndexes($table);
        foreach ($indexes as $index) {
            if ($index['name'] === $indexName) {
                return true;
            }
        }
        return false;
    }
};
