<?php

namespace App\Filament\Portal\Resources\MyTicketResource\Pages;

use App\Filament\Portal\Resources\MyTicketResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateMyTicket extends CreateRecord
{
    protected static string $resource = MyTicketResource::class;

    public function getTitle(): string | Htmlable
    {
        return 'Create My Ticket';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['customer_id'] = auth()->user()->customer->id;
        $data['status'] = 'open';
        return $data;
    }
}
