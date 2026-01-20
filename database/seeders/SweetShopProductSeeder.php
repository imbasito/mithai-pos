<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Sweet Shop Product Seeder
 * Seeds the original 10 sweet shop products
 */
class SweetShopProductSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Clear existing products
        DB::table('products')->truncate();
        DB::table('categories')->truncate();
        DB::table('brands')->truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Create Categories
        $categories = [
            'Sweets' => Category::create(['name' => 'Sweets']),
            'Dry Sweets' => Category::create(['name' => 'Dry Sweets']),
            'Fresh' => Category::create(['name' => 'Fresh']),
        ];

        // Create Brands
        $brands = [
            'Mithai House' => Brand::create(['name' => 'Mithai House']),
            'Premium Sweets' => Brand::create(['name' => 'Premium Sweets']),
            'Local Halwai' => Brand::create(['name' => 'Local Halwai']),
        ];

        // Get units (use title field instead of name)
        $units = [
            'kg' => Unit::where('title', 'kg')->first() ?? Unit::create(['title' => 'kg', 'short_name' => 'kg']),
            'pcs' => Unit::where('title', 'pcs')->first() ?? Unit::create(['title' => 'pcs', 'short_name' => 'pc']),
            'gm' => Unit::where('title', 'gm')->first() ?? Unit::create(['title' => 'gm', 'short_name' => 'g']),
        ];

        // Sweet Shop Products
        $products = [
            [
                'name' => 'Gulab Jamun (1kg)',
                'sku' => 'GJ-001',
                'description' => 'Premium rose-flavored milk balls in sugar syrup',
                'category_id' => $categories['Sweets']->id,
                'brand_id' => $brands['Mithai House']->id,
                'unit_id' => $units['kg']->id,
                'price' => 450,
                'discount' => 10,
                'discount_type' => 'percentage',
                'purchase_price' => 350,
                'quantity' => 50,
                'expire_date' => '2026-12-31',
                'status' => 1,
            ],
            [
                'name' => 'Rasgulla (500g)',
                'sku' => 'RG-001',
                'description' => 'Soft spongy cottage cheese balls',
                'category_id' => $categories['Sweets']->id,
                'brand_id' => $brands['Mithai House']->id,
                'unit_id' => $units['pcs']->id,
                'price' => 280,
                'discount' => 0,
                'discount_type' => 'fixed',
                'purchase_price' => 200,
                'quantity' => 40,
                'expire_date' => '2026-06-30',
                'status' => 1,
            ],
            [
                'name' => 'Kaju Katli (250g)',
                'sku' => 'KK-001',
                'description' => 'Diamond-shaped cashew fudge',
                'category_id' => $categories['Dry Sweets']->id,
                'brand_id' => $brands['Premium Sweets']->id,
                'unit_id' => $units['gm']->id,
                'price' => 550,
                'discount' => 5,
                'discount_type' => 'percentage',
                'purchase_price' => 450,
                'quantity' => 30,
                'expire_date' => '2026-09-15',
                'status' => 1,
            ],
            [
                'name' => 'Barfi Mix (500g)',
                'sku' => 'BM-001',
                'description' => 'Assorted milk barfi collection',
                'category_id' => $categories['Sweets']->id,
                'brand_id' => $brands['Mithai House']->id,
                'unit_id' => $units['gm']->id,
                'price' => 380,
                'discount' => 0,
                'discount_type' => 'fixed',
                'purchase_price' => 280,
                'quantity' => 25,
                'expire_date' => '2026-08-20',
                'status' => 1,
            ],
            [
                'name' => 'Jalebi (250g)',
                'sku' => 'JL-001',
                'description' => 'Crispy spiral-shaped sweet',
                'category_id' => $categories['Fresh']->id,
                'brand_id' => $brands['Local Halwai']->id,
                'unit_id' => $units['gm']->id,
                'price' => 180,
                'discount' => 0,
                'discount_type' => 'fixed',
                'purchase_price' => 120,
                'quantity' => 60,
                'expire_date' => '2026-03-15',
                'status' => 1,
            ],
            [
                'name' => 'Laddu Besan (1kg)',
                'sku' => 'LB-001',
                'description' => 'Traditional gram flour laddu',
                'category_id' => $categories['Sweets']->id,
                'brand_id' => $brands['Mithai House']->id,
                'unit_id' => $units['kg']->id,
                'price' => 400,
                'discount' => 15,
                'discount_type' => 'percentage',
                'purchase_price' => 300,
                'quantity' => 35,
                'expire_date' => '2026-10-30',
                'status' => 1,
            ],
            [
                'name' => 'Peda (500g)',
                'sku' => 'PD-001',
                'description' => 'Soft khoya-based sweet',
                'category_id' => $categories['Sweets']->id,
                'brand_id' => $brands['Premium Sweets']->id,
                'unit_id' => $units['gm']->id,
                'price' => 320,
                'discount' => 0,
                'discount_type' => 'fixed',
                'purchase_price' => 240,
                'quantity' => 45,
                'expire_date' => '2026-07-25',
                'status' => 1,
            ],
            [
                'name' => 'Soan Papdi (400g)',
                'sku' => 'SP-001',
                'description' => 'Flaky layered sweet',
                'category_id' => $categories['Dry Sweets']->id,
                'brand_id' => $brands['Mithai House']->id,
                'unit_id' => $units['gm']->id,
                'price' => 220,
                'discount' => 10,
                'discount_type' => 'fixed',
                'purchase_price' => 150,
                'quantity' => 55,
                'expire_date' => '2027-01-15',
                'status' => 1,
            ],
            [
                'name' => 'Milk Cake (1kg)',
                'sku' => 'MC-001',
                'description' => 'Dense caramelized milk sweet',
                'category_id' => $categories['Sweets']->id,
                'brand_id' => $brands['Local Halwai']->id,
                'unit_id' => $units['kg']->id,
                'price' => 480,
                'discount' => 0,
                'discount_type' => 'fixed',
                'purchase_price' => 380,
                'quantity' => 20,
                'expire_date' => '2026-05-10',
                'status' => 1,
            ],
            [
                'name' => 'Kalakand (500g)',
                'sku' => 'KL-001',
                'description' => 'Grainy milk cake sweet',
                'category_id' => $categories['Sweets']->id,
                'brand_id' => $brands['Premium Sweets']->id,
                'unit_id' => $units['gm']->id,
                'price' => 350,
                'discount' => 5,
                'discount_type' => 'percentage',
                'purchase_price' => 270,
                'quantity' => 40,
                'expire_date' => '2026-04-20',
                'status' => 1,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        $this->command->info('✅ Sweet Shop Products seeded successfully!');
        $this->command->info('   3 Categories, 3 Brands, 10 Products created');
    }
}
