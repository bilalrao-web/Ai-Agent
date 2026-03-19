<?php

namespace App\Filament\Portal\Resources;

use App\Filament\Portal\Resources\RoleResource\Pages\ListRoles;
use Spatie\Permission\Models\Role;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class RoleResource extends Resource
{
    protected static ?string $model          = Role::class;
    protected static ?string $navigationLabel = 'Roles';
    protected static ?string $navigationIcon  = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'Roles & Permissions';
    protected static ?int    $navigationSort  = 6;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermissionTo('view_any_roles') ?? false;
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
                TextColumn::make('permissions_count')
                    ->label('Permissions')
                    ->counts('permissions'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
        ];
    }
}
