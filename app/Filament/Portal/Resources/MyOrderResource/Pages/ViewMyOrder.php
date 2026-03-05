<?php

namespace App\Filament\Portal\Resources\MyOrderResource\Pages;

use App\Filament\Portal\Resources\MyOrderResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewMyOrder extends ViewRecord
{
    protected static string $resource = MyOrderResource::class;

    public function getTitle(): string | Htmlable
    {
        return 'View Order';
    }

    protected function authorizeAccess(): void
    {
        $customer = auth()->user()?->customer;
        if (! $customer || $this->record->customer_id !== $customer->id) {
            abort(403, 'You do not have permission to view this order.');
        }
    }
}
