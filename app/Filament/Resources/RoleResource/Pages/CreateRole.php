<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

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

    protected function afterCreate(): void
    {
        $role = $this->record;
        $data = $this->form->getState();

        $selected = [];

        foreach (RoleResource::moduleGroups() as $moduleName => $modulePerms) {
            $fieldKey = RoleResource::moduleFieldKey($moduleName);
            $checked = $data[$fieldKey] ?? [];
            $selected = array_merge($selected, $checked);
        }

        $role->syncPermissions(array_unique($selected));
    }
}
