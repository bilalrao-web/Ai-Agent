<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\PermissionTypeEnum;

class UserPolicy
{
    public const PERMISSIONS = [
        ['name' => 'view_any_users', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'view_user', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'create_user', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'update_user', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'delete_user', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'force_delete_user', 'type' => PermissionTypeEnum::WEB],
    ];

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_any_users');
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasPermissionTo('view_user');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_user');
    }

    public function update(User $user, User $model): bool
    {
        return $user->hasPermissionTo('update_user');
    }

    public function delete(User $user, User $model): bool
    {
        return $user->hasPermissionTo('delete_user');
    }

    public function restore(User $user, User $model): bool
    {
        return true;
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $user->hasPermissionTo('force_delete_user');
    }
}
