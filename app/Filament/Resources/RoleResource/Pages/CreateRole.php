<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected array $selectedPermissions = [];

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Role created')
            ->body('Permissions saved successfully.');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $selected = [];

        foreach (RoleResource::moduleGroups() as $moduleName => $modulePerms) {
            $fieldKey = RoleResource::moduleFieldKey($moduleName);
            $checked = $data[$fieldKey] ?? [];

            if (is_array($checked)) {
                $selected = array_merge($selected, $checked);
            }
        }

        $this->selectedPermissions = array_unique($selected);

        foreach (RoleResource::moduleGroups() as $moduleName => $modulePerms) {
            $fieldKey = RoleResource::moduleFieldKey($moduleName);
            unset($data[$fieldKey]);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $role = $this->record;
        
        if (!empty($this->selectedPermissions)) {
            $role->syncPermissions($this->selectedPermissions);
        }
    }
}
