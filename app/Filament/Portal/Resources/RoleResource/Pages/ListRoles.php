<?php

namespace App\Filament\Portal\Resources\RoleResource\Pages;

use App\Filament\Portal\Resources\RoleResource;
use Filament\Resources\Pages\ListRecords;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return []; // no create button
    }
}
