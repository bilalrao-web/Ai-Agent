<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Customers
            'view_any_customers',
            'view_customer',
            'create_customer',
            'update_customer',
            'delete_customer',
            // Orders
            'view_any_orders',
            'view_order',
            'create_order',
            'update_order',
            'delete_order',
            // Tickets
            'view_any_tickets',
            'view_ticket',
            'create_ticket',
            'update_ticket',
            'delete_ticket',
            // Call logs
            'view_any_calls',
            'view_call',
            // FAQs
            'view_any_faqs',
            'view_faq',
            'create_faq',
            'update_faq',
            'delete_faq',
            // Users
            'view_any_users',
            'view_user',
            'create_user',
            'update_user',
            'delete_user',
            // Own (for portal / customer scope)
            'view_own_tickets',
            'view_own_orders',
            'view_own_calls',
            // Roles & Permissions (admin UI)
            'manage_roles',
            'manage_permissions',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $allResourcePermissions = [
            'view_any_customers', 'view_customer', 'create_customer', 'update_customer', 'delete_customer',
            'view_any_orders', 'view_order', 'create_order', 'update_order', 'delete_order',
            'view_any_tickets', 'view_ticket', 'create_ticket', 'update_ticket', 'delete_ticket',
            'view_any_calls', 'view_call',
            'view_any_faqs', 'view_faq', 'create_faq', 'update_faq', 'delete_faq',
            'view_any_users', 'view_user', 'create_user', 'update_user', 'delete_user',
            'view_own_tickets', 'view_own_orders', 'view_own_calls',
            'manage_roles', 'manage_permissions',
        ];

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        // Admin gets ALL permissions (same as super_admin, but super_admin bypasses via Gate::before)
        // This ensures admin can access all resources in the admin panel
        $admin->syncPermissions(Permission::all());

        $supportAgent = Role::firstOrCreate(['name' => 'support_agent', 'guard_name' => 'web']);
        $supportAgent->syncPermissions([
            'view_any_calls', 'view_call',
            'view_any_tickets', 'view_ticket', 'create_ticket', 'update_ticket',
        ]);

        $customer = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);
        // Default portal module permissions (super admin can toggle via RoleResource)
        $customer->syncPermissions([
            'view_any_calls',      // ✅ My Call History (ON by default)
            'view_call',
            'view_any_orders',     // ✅ My Orders (ON by default)
            'view_order',
            'view_any_tickets',    // ✅ My Tickets (ON by default)
            'view_ticket',
            'create_ticket',
            // 'view_any_faqs'     // ❌ FAQs (OFF by default — super admin enables via RoleResource)
        ]);
    }
}
