<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Role updated')
            ->body('Permissions saved successfully.');
    }

    protected function afterSave(): void
    {
        $role = $this->record;
        $data = $this->form->getState();

        if (in_array($role->name, ['super_admin', 'admin'], true)) {
            return;
        }

        $selected = [];

        foreach (RoleResource::moduleGroups() as $moduleName => $modulePerms) {
            $fieldKey = RoleResource::moduleFieldKey($moduleName);
            $checked = $data[$fieldKey] ?? [];

            if (is_array($checked)) {
                $selected = array_merge($selected, $checked);
            }
        }

        $role->syncPermissions(array_unique($selected));
    }
}
