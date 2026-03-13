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

class RoleAndPermissionSeeder extends Seeder
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
        $allPermissions = [];
        
        foreach ($this->policies as $policy) {
            foreach ($policy::PERMISSIONS as $perm) {
                if ($perm['type']->value === 'web') {
                    $allPermissions[] = $perm['name'];
                    Permission::updateOrCreate(
                        ['name' => $perm['name'], 'guard_name' => 'web'],
                        ['name' => $perm['name'], 'guard_name' => 'web']
                    );
                }
            }
        }

        Permission::updateOrCreate(
            ['name' => 'manage_roles', 'guard_name' => 'web'],
            ['name' => 'manage_roles', 'guard_name' => 'web']
        );
        Permission::updateOrCreate(
            ['name' => 'manage_permissions', 'guard_name' => 'web'],
            ['name' => 'manage_permissions', 'guard_name' => 'web']
        );
        
        $allPermissions[] = 'manage_roles';
        $allPermissions[] = 'manage_permissions';

        $superAdmin = Role::updateOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'web'],
            ['name' => 'super_admin', 'guard_name' => 'web']
        );
        $superAdmin->syncPermissions(Permission::all());

        $admin = Role::updateOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'admin', 'guard_name' => 'web']
        );
        $admin->syncPermissions(Permission::all());

        $supportAgentPermissions = [];
        foreach ([CallLogPolicy::class, TicketPolicy::class] as $policy) {
            foreach ($policy::PERMISSIONS as $perm) {
                if (in_array($perm['name'], [
                    'view_any_calls',
                    'view_call',
                    'view_any_tickets',
                    'view_ticket',
                    'create_ticket',
                    'update_ticket',
                ])) {
                    $supportAgentPermissions[] = $perm['name'];
                }
            }
        }

        $supportAgent = Role::updateOrCreate(
            ['name' => 'support_agent', 'guard_name' => 'web'],
            ['name' => 'support_agent', 'guard_name' => 'web']
        );
        $supportAgent->syncPermissions($supportAgentPermissions);

        $customerPermissions = [];
        foreach ([TicketPolicy::class, OrderPolicy::class, CallLogPolicy::class] as $policy) {
            foreach ($policy::PERMISSIONS as $perm) {
                if (in_array($perm['name'], [
                    'view_any_tickets',
                    'create_ticket',
                    'view_ticket',
                    'view_any_orders',
                    'view_order',
                    'view_any_calls',
                    'view_call',
                ])) {
                    $customerPermissions[] = $perm['name'];
                }
            }
        }

        $customer = Role::updateOrCreate(
            ['name' => 'customer', 'guard_name' => 'web'],
            ['name' => 'customer', 'guard_name' => 'web']
        );
        $customer->syncPermissions($customerPermissions);
    }
}
