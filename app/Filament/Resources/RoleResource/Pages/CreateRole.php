<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    /** @var array<int> */
    protected array $pendingPermissionIds = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->pendingPermissionIds = $data['permissions'] ?? [];
        unset($data['permissions']);
        return $data;
    }

    protected function afterCreate(): void
    {
        if (! empty($this->pendingPermissionIds)) {
            $this->record->syncPermissions($this->pendingPermissionIds);
            
            // Clear permission cache so changes take effect immediately
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        }
    }
}
