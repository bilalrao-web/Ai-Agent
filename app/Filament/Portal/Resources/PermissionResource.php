<?php

namespace App\Filament\Portal\Resources;

use App\Filament\Portal\Resources\PermissionResource\Pages\ListPermissions;
use Spatie\Permission\Models\Permission;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class PermissionResource extends Resource
{
    protected static ?string $model          = Permission::class;
    protected static ?string $navigationLabel = 'Permissions';
    protected static ?string $navigationIcon  = 'heroicon-o-key';
    protected static ?string $navigationGroup = 'Roles & Permissions';
    protected static ?int    $navigationSort  = 7;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermissionTo('view_any_permissions') ?? false;
    }

    public static function canCreate(): bool        { return false; }
    public static function canEdit($record): bool   { return false; }
    public static function canDelete($record): bool { return false; }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('guard_name')->label('Guard'),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPermissions::route('/'),
        ];
    }
}
