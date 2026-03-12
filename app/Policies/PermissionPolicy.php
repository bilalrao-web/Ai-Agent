<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use App\Enums\PermissionTypeEnum;

class PermissionPolicy
{
    public const PERMISSIONS = [
        ['name' => 'view_any_permissions', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'view_permission', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'create_permission', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'update_permission', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'delete_permission', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'force_delete_permission', 'type' => PermissionTypeEnum::WEB],
    ];

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_any_permissions');
    }

    public function view(User $user, Permission $permission): bool
    {
        return $user->hasPermissionTo('view_permission');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_permission');
    }

    public function update(User $user, Permission $permission): bool
    {
        return $user->hasPermissionTo('update_permission');
    }

    public function delete(User $user, Permission $permission): bool
    {
        return $user->hasPermissionTo('delete_permission');
    }

    public function restore(User $user, Permission $permission): bool
    {
        return true;
    }

    public function forceDelete(User $user, Permission $permission): bool
    {
        return $user->hasPermissionTo('force_delete_permission');
    }
}
