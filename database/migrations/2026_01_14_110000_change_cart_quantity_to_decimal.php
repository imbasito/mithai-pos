<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Change cart quantity from integer to decimal for weight-based selling
     * Uses raw SQL to avoid doctrine/dbal dependency
     */
    public function up(): void
    {
        // Direct SQL to modify column type - works without doctrine/dbal
        DB::statement('ALTER TABLE pos_carts MODIFY COLUMN quantity DECIMAL(10,3) DEFAULT 1.000');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE pos_carts MODIFY COLUMN quantity INT DEFAULT 1');
    }
};
