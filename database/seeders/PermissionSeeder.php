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
        foreach ($this->policies as $policy) {
            foreach ($policy::PERMISSIONS as $perm) {
                Permission::firstOrCreate(
                    ['name' => $perm['name'], 'guard_name' => $perm['type']->value]
                );
            }
        }

        Permission::firstOrCreate(['name' => 'manage_roles', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage_permissions', 'guard_name' => 'web']);

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        $customerRole = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        $defaultCustomerPermissions = [
            'view_any_tickets',
            'create_ticket',
            'view_ticket',
            'view_any_orders',
            'view_order',
            'view_any_calls',
            'view_call',
        ];

        $customerRole->syncPermissions($defaultCustomerPermissions);
    }
}
