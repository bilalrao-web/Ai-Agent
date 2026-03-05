<?php

namespace App\Filament\Portal\Resources\MyCallHistoryResource\Pages;

use App\Filament\Portal\Resources\MyCallHistoryResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewMyCallHistory extends ViewRecord
{
    protected static string $resource = MyCallHistoryResource::class;

    public function getTitle(): string | Htmlable
    {
        return 'View Call History';
    }

    protected function authorizeAccess(): void
    {
        $customer = auth()->user()?->customer;
        if (! $customer || $this->record->customer_id !== $customer->id) {
            abort(403, 'You do not have permission to view this call log.');
        }
    }
}
