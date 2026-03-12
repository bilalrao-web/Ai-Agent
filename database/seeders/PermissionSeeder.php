<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Policies\CallLogPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\OrderPolicy;
use App\Policies\TicketPolicy;
use App\Policies\UserPolicy;
use App\Policies\RolePolicy;
use App\Policies\PermissionPolicy;
use App\Policies\FaqPolicy;

class PermissionSeeder extends Seeder
{
    protected array $policies = [
        CallLogPolicy::class,
        CustomerPolicy::class,
        OrderPolicy::class,
        TicketPolicy::class,
        UserPolicy::class,
        RolePolicy::class,
        PermissionPolicy::class,
        FaqPolicy::class,
    ];

    public function run(): void
    {
        // 1. Create all permissions from policy constants
        foreach ($this->policies as $policy) {
            foreach ($policy::PERMISSIONS as $perm) {
                Permission::firstOrCreate(
                    ['name' => $perm['name'], 'guard_name' => $perm['type']->value]
                );
            }
        }

        // 2. Create additional management permissions (used in Filament resources)
        Permission::firstOrCreate(['name' => 'manage_roles', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage_permissions', 'guard_name' => 'web']);

        // 3. Create Super Admin role + give ALL permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        // 4. Create Customer role with DEFAULT portal access
        $customerRole = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        // Default: give customer these portal modules (ON by default)
        $defaultCustomerPermissions = [
            'view_any_tickets',   // ✅ ON by default
            'create_ticket',
            'view_ticket',
            'view_any_orders',    // ✅ ON by default
            'view_order',
            'view_any_calls',     // ✅ ON by default
            'view_call',
            // 'view_any_faqs'    // ❌ OFF by default — super admin enables it
        ];

        $customerRole->syncPermissions($defaultCustomerPermissions);
    }
}
