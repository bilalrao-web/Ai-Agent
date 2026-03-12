<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Enums\PermissionTypeEnum;

class RolePolicy
{
    public const PERMISSIONS = [
        ['name' => 'view_any_roles', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'view_role', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'create_role', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'update_role', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'delete_role', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'force_delete_role', 'type' => PermissionTypeEnum::WEB],
    ];

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_any_roles');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('view_role');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_role');
    }

    public function update(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('update_role');
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('delete_role');
    }

    public function restore(User $user, Role $role): bool
    {
        return true;
    }

    public function forceDelete(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('force_delete_role');
    }
}
