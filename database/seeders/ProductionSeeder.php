<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use App\Models\Supplier;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * Production Seeder - Clean setup for client deployment
 * 
 * This seeder creates:
 * - Admin, Cashier, Sales accounts
 * - Default walking customer
 * - Default supplier
 * - ALL required roles and permissions
 * - Basic units and currency
 */
class ProductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ====== ADMIN USER ======
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@spos.com',
            'password' => bcrypt('admin123'),
            'username' => 'admin'
        ]);

        // ====== DEFAULT CUSTOMER ======
        Customer::create([
            'name' => "Walking Customer",
            'phone' => "0000000000",
        ]);

        // ====== DEFAULT SUPPLIER ======
        Supplier::create([
            'name' => "Default Supplier",
            'phone' => "0000000000",
        ]);

        // ====== ROLES ======
        $adminRole = Role::create(['name' => 'Admin']);
        Role::create(['name' => 'cashier']);
        Role::create(['name' => 'sales_associate']);

        // ====== ALL PERMISSIONS (Complete from sidebar audit) ======
        $permissions = [
            // Dashboard
            'dashboard_view',
            
            // Customers
            'customer_create', 'customer_view', 'customer_update', 'customer_delete', 'customer_sales',
            
            // Suppliers
            'supplier_create', 'supplier_view', 'supplier_update', 'supplier_delete',
            
            // Products (includes inventory)
            'product_create', 'product_view', 'product_update', 'product_delete', 
            'product_import', 'product_purchase',
            
            // Brands
            'brand_create', 'brand_view', 'brand_update', 'brand_delete',
            
            // Categories
            'category_create', 'category_view', 'category_update', 'category_delete',
            
            // Units
            'unit_create', 'unit_view', 'unit_update', 'unit_delete',
            
            // Sales
            'sale_create', 'sale_view', 'sale_update', 'sale_delete', 'sale_edit',
            
            // Purchases
            'purchase_create', 'purchase_view', 'purchase_update', 'purchase_delete',
            
            // Expenses
            'expense_create', 'expense_view', 'expense_update', 'expense_delete',
            
            // Reports
            'reports_summary', 'reports_sales', 'reports_inventory',
            
            // Currency
            'currency_create', 'currency_view', 'currency_update', 'currency_delete', 'currency_set_default',
            
            // Roles & Permissions
            'role_create', 'role_view', 'role_update', 'role_delete', 'permission_view',
            
            // Users
            'user_create', 'user_view', 'user_update', 'user_delete', 'user_suspend',
            
            // Website Settings
            'website_settings', 'contact_settings', 'socials_settings',
            'style_settings', 'custom_settings', 'notification_settings',
            'website_status_settings', 'invoice_settings',
        ];

        // Create all permissions and give to admin
        foreach ($permissions as $permission) {
            $perm = Permission::create(['name' => $permission]);
            $adminRole->givePermissionTo($perm);
        }

        // Assign admin role to user
        $admin->syncRoles($adminRole);

        // ====== CASHIER USER ======
        $cashier = User::create([
            'name' => 'Cashier',
            'email' => 'cashier@spos.com',
            'password' => bcrypt('cashier123'),
            'username' => 'cashier'
        ]);

        // Cashier permissions (original setup)
        $cashierRole = Role::where('name', 'cashier')->first();
        $cashierPermissions = [
            'sale_create', 'sale_view',
            'customer_view',
            'product_create', 'product_view', 'product_update', 'product_delete', 'product_import',
        ];
        foreach ($cashierPermissions as $permName) {
            $perm = Permission::where('name', $permName)->first();
            if ($perm) {
                $cashierRole->givePermissionTo($perm);
            }
        }
        $cashier->syncRoles($cashierRole);

        // ====== SALES USER ======
        $sales = User::create([
            'name' => 'Sales',
            'email' => 'sales@spos.com',
            'password' => bcrypt('sales123'),
            'username' => 'sales'
        ]);

        // Sales permissions (original setup)
        $salesRole = Role::where('name', 'sales_associate')->first();
        $salesPermissions = [
            'sale_create', 'sale_view',
        ];
        foreach ($salesPermissions as $permName) {
            $perm = Permission::where('name', $permName)->first();
            if ($perm) {
                $salesRole->givePermissionTo($perm);
            }
        }
        $sales->syncRoles($salesRole);

        // ====== UNITS ======
        $this->call(UnitSeeder::class);

        // ====== CURRENCY ======
        $this->call(CurrencySeeder::class);

        $this->command->info('');
        $this->command->info('✅ Production seeder completed!');
        $this->command->info('');
        $this->command->info('   ADMIN:    admin@spos.com    | admin123');
        $this->command->info('   CASHIER:  cashier@spos.com  | cashier123');
        $this->command->info('   SALES:    sales@spos.com    | sales123');
        $this->command->info('');
    }
}
