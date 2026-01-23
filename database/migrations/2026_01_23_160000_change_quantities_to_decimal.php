<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Change quantity columns from integer to decimal for weight-based selling
     */
    public function up(): void
    {
        // Modify products table quantity
        DB::statement('ALTER TABLE products MODIFY COLUMN quantity DECIMAL(10,3) DEFAULT 0.000');
        
        // Modify order_products table quantity
        DB::statement('ALTER TABLE order_products MODIFY COLUMN quantity DECIMAL(10,3) DEFAULT 1.000');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE products MODIFY COLUMN quantity INT DEFAULT 0');
        DB::statement('ALTER TABLE order_products MODIFY COLUMN quantity INT DEFAULT 1');
    }
};
