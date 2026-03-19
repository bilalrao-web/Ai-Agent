<?php

namespace App\Filament\Portal\Resources\UserResource\Pages;

use App\Filament\Portal\Resources\UserResource;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
