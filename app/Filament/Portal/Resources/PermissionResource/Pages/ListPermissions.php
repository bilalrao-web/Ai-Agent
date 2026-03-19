<?php

namespace App\Filament\Portal\Resources\PermissionResource\Pages;

use App\Filament\Portal\Resources\PermissionResource;
use Filament\Resources\Pages\ListRecords;

class ListPermissions extends ListRecords
{
    protected static string $resource = PermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
