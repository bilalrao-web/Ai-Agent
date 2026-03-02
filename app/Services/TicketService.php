<?php

namespace App\Services;

use App\Models\Ticket;

class TicketService
{
    public function createTicket(int $customerId, string $issueType, string $description): Ticket
    {
        return Ticket::create([
            'customer_id' => $customerId,
            'issue_type' => $issueType,
            'description' => $description,
            'status' => 'open',
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Ticket>
     */
    public function getOpenTickets(int $customerId)
    {
        return Ticket::where('customer_id', $customerId)
            ->whereIn('status', ['open', 'in_progress'])
            ->orderByDesc('created_at')
            ->get();
    }
}
