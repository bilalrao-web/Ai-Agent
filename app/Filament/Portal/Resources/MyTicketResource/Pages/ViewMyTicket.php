<?php

namespace App\Filament\Portal\Resources\MyTicketResource\Pages;

use App\Filament\Portal\Resources\MyTicketResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewMyTicket extends ViewRecord
{
    protected static string $resource = MyTicketResource::class;

    public function getTitle(): string | Htmlable
    {
        return 'View My Ticket';
    }

    protected function authorizeAccess(): void
    {
        $customer = auth()->user()?->customer;
        if (! $customer || $this->record->customer_id !== $customer->id) {
            abort(403, 'You do not have permission to view this ticket.');
        }
    }
}
