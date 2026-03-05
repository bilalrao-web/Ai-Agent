<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    /** @var array<int> */
    protected array $pendingPermissionIds = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['permissions'] = $this->record->permissions()->pluck('id')->toArray();
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Store permission IDs before unsetting
        // If permissions are not in the form data, use current permissions (user didn't change them)
        $this->pendingPermissionIds = $data['permissions'] ?? $this->record->permissions()->pluck('id')->toArray();
        unset($data['permissions']);
        return $data;
    }

    protected function afterSave(): void
    {
        // Always sync permissions (even if empty array - means remove all permissions)
        // This ensures permissions are always saved correctly
        $this->record->syncPermissions($this->pendingPermissionIds ?? []);
        
        // Clear permission cache so changes take effect immediately
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
