<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Customer;
use App\Enums\PermissionTypeEnum;

class CustomerPolicy
{
    public const PERMISSIONS = [
        ['name' => 'view_any_customers', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'view_customer', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'create_customer', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'update_customer', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'delete_customer', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'force_delete_customer', 'type' => PermissionTypeEnum::WEB],
    ];

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_any_customers');
    }

    public function view(User $user, Customer $customer): bool
    {
        return $user->hasPermissionTo('view_customer');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_customer');
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->hasPermissionTo('update_customer');
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $user->hasPermissionTo('delete_customer');
    }

    public function restore(User $user, Customer $customer): bool
    {
        return true;
    }

    public function forceDelete(User $user, Customer $customer): bool
    {
        return $user->hasPermissionTo('force_delete_customer');
    }
}
