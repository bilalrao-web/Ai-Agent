<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CallLog;
use App\Enums\PermissionTypeEnum;

class CallLogPolicy
{
    public const PERMISSIONS = [
        ['name' => 'view_any_calls', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'view_call', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'create_call', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'update_call', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'delete_call', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'force_delete_call', 'type' => PermissionTypeEnum::WEB],
    ];

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_any_calls');
    }

    public function view(User $user, CallLog $callLog): bool
    {
        return $user->hasPermissionTo('view_call');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_call');
    }

    public function update(User $user, CallLog $callLog): bool
    {
        return $user->hasPermissionTo('update_call');
    }

    public function delete(User $user, CallLog $callLog): bool
    {
        return $user->hasPermissionTo('delete_call');
    }

    public function restore(User $user, CallLog $callLog): bool
    {
        return true;
    }

    public function forceDelete(User $user, CallLog $callLog): bool
    {
        return $user->hasPermissionTo('force_delete_call');
    }
}
