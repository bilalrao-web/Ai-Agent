<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;
use App\Enums\PermissionTypeEnum;

class OrderPolicy
{
    public const PERMISSIONS = [
        ['name' => 'view_any_orders', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'view_order', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'create_order', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'update_order', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'delete_order', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'force_delete_order', 'type' => PermissionTypeEnum::WEB],
    ];

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_any_orders');
    }

    public function view(User $user, Order $order): bool
    {
        return $user->hasPermissionTo('view_order');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_order');
    }

    public function update(User $user, Order $order): bool
    {
        return $user->hasPermissionTo('update_order');
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->hasPermissionTo('delete_order');
    }

    public function restore(User $user, Order $order): bool
    {
        return true;
    }

    public function forceDelete(User $user, Order $order): bool
    {
        return $user->hasPermissionTo('force_delete_order');
    }
}
