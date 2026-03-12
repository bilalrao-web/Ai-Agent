<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ticket;
use App\Enums\PermissionTypeEnum;

class TicketPolicy
{
    public const PERMISSIONS = [
        ['name' => 'view_any_tickets', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'view_ticket', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'create_ticket', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'update_ticket', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'delete_ticket', 'type' => PermissionTypeEnum::WEB],
        ['name' => 'force_delete_ticket', 'type' => PermissionTypeEnum::WEB],
    ];

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view_any_tickets');
    }

    public function view(User $user, Ticket $ticket): bool
    {
        return $user->hasPermissionTo('view_ticket');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_ticket');
    }

    public function update(User $user, Ticket $ticket): bool
    {
        return $user->hasPermissionTo('update_ticket');
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->hasPermissionTo('delete_ticket');
    }

    public function restore(User $user, Ticket $ticket): bool
    {
        return true;
    }

    public function forceDelete(User $user, Ticket $ticket): bool
    {
        return $user->hasPermissionTo('force_delete_ticket');
    }
}
