<?php

namespace App\Policies;

use App\Models\CallLog;
use App\Models\User;

class CallLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_calls');
    }

    public function view(User $user, CallLog $callLog): bool
    {
        return $user->can('view_call');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, CallLog $callLog): bool
    {
        return false;
    }

    public function delete(User $user, CallLog $callLog): bool
    {
        return false;
    }
}
