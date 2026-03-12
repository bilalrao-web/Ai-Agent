<?php

namespace App\Filament\Portal\Resources\FaqResource\Pages;

use App\Filament\Portal\Resources\FaqResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFaq extends ViewRecord
{
    protected static string $resource = FaqResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
