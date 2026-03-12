<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Faq;
use App\Enums\PermissionTypeEnum;

class FaqPolicy
{
    public const PERMISSIONS = [
        ['name' => 'view_any_faqs', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'view_faq', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'create_faq', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'update_faq', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'delete_faq', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'force_delete_faq', 'type' => PermissionTypeEnum::WEB],
    ];

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_any_faqs');
    }

    public function view(User $user, Faq $faq): bool
    {
        return $user->hasPermissionTo('view_faq');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_faq');
    }

    public function update(User $user, Faq $faq): bool
    {
        return $user->hasPermissionTo('update_faq');
    }

    public function delete(User $user, Faq $faq): bool
    {
        return $user->hasPermissionTo('delete_faq');
    }

    public function restore(User $user, Faq $faq): bool
    {
        return true;
    }

    public function forceDelete(User $user, Faq $faq): bool
    {
        return $user->hasPermissionTo('force_delete_faq');
    }
}
