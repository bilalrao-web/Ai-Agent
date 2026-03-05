<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Resources\PermissionResource;
use Filament\Actions;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewPermission extends ViewRecord
{
    protected static string $resource = PermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                \Filament\Infolists\Components\Section::make('Permission Information')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Permission Name')
                            ->weight('bold')
                            ->size('lg')
                            ->copyable()
                            ->copyMessage('Permission name copied!')
                            ->copyMessageDuration(1500),
                        TextEntry::make('guard_name')
                            ->label('Guard Name')
                            ->badge()
                            ->color('gray'),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('d/M/Y H:i'),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime('d/M/Y H:i'),
                    ])
                    ->columns(2),
            ]);
    }
}
