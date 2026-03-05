<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

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
                \Filament\Infolists\Components\Section::make('Role Information')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Role Name')
                            ->weight('bold')
                            ->size('lg'),
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
                \Filament\Infolists\Components\Section::make('Permissions')
                    ->schema([
                        TextEntry::make('permissions.name')
                            ->label('Assigned Permissions')
                            ->badge()
                            ->separator(',')
                            ->formatStateUsing(function ($state) {
                                if (is_array($state)) {
                                    return array_map(function ($name) {
                                        return str_replace('_', ' ', ucwords($name, '_'));
                                    }, $state);
                                }
                                return $state;
                            })
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }
}
