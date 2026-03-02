<?php

namespace App\Filament\Portal\Resources\MyTicketResource\Pages;

use App\Filament\Portal\Resources\MyTicketResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMyTicket extends CreateRecord
{
    protected static string $resource = MyTicketResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['customer_id'] = auth()->user()->customer->id;
        $data['status'] = 'open';
        return $data;
    }
}
