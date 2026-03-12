<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Illuminate\Support\Str;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Roles';
    protected static ?string $modelLabel = 'Role';
    protected static ?string $navigationGroup = 'Roles & Permissions';

    /**
     * Group permissions by module for a Propday-style UI.
     *
     * Each key is a human-friendly module name, and the value is
     * an array of permission names that belong to that module.
     */
    public static function moduleGroups(): array
    {
        return [
            'Customers' => [
                'view_any_customers',
                'view_customer',
                'create_customer',
                'update_customer',
                'delete_customer',
                'force_delete_customer',
            ],
            'Orders' => [
                'view_any_orders',
                'view_order',
                'create_order',
                'update_order',
                'delete_order',
                'force_delete_order',
            ],
            'Tickets' => [
                'view_any_tickets',
                'view_ticket',
                'create_ticket',
                'update_ticket',
                'delete_ticket',
                'force_delete_ticket',
                'view_own_tickets',
            ],
            'Call Logs' => [
                'view_any_calls',
                'view_call',
                'view_own_calls',
            ],
            'FAQs' => [
                'view_any_faqs',
                'view_faq',
                'create_faq',
                'update_faq',
                'delete_faq',
                'force_delete_faq',
            ],
            'Users' => [
                'view_any_users',
                'view_user',
                'create_user',
                'update_user',
                'delete_user',
                'force_delete_user',
            ],
            'Roles' => [
                'view_any_roles',
                'view_role',
                'create_role',
                'update_role',
                'delete_role',
                'manage_roles',
            ],
            'Permissions' => [
                'view_any_permissions',
                'view_permission',
                'create_permission',
                'update_permission',
                'delete_permission',
                'manage_permissions',
            ],
        ];
    }

    /**
     * Build a safe form field key for a given module name.
     */
    public static function moduleFieldKey(string $moduleName): string
    {
        return 'perms_' . Str::slug($moduleName, '_');
    }

    public static function form(Form $form): Form
    {
        $permissionSections = [];

        foreach (self::moduleGroups() as $moduleName => $modulePerms) {
            $options = Permission::whereIn('name', $modulePerms)
                ->orderBy('name')
                ->pluck('name', 'name')
                ->toArray();

            if (empty($options)) {
                continue;
            }

            $fieldKey = self::moduleFieldKey($moduleName);

            $permissionSections[] = Section::make($moduleName)
                ->schema([
                    CheckboxList::make($fieldKey)
                        ->label('')
                        ->options($options)
                        ->columns(3)
                        ->gridDirection('row')
                        ->bulkToggleable()
                        ->default([])
                        ->afterStateHydrated(function (CheckboxList $component, ?Role $record) use ($options): void {
                            if (! $record) {
                                $component->state([]);
                                return;
                            }

                            $active = $record->permissions
                                ->pluck('name')
                                ->intersect(array_keys($options))
                                ->values()
                                ->toArray();

                            $component->state($active);
                        }),
                ])
                ->collapsible()
                ->collapsed(false);
        }

        return $form->schema([
            Section::make('Role Information')
                ->schema([
                    Section::make('Role Details')
                        ->schema([
                            TextInput::make('name')
                                ->label('Role Name')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->disabled(fn ($record) => $record?->name === 'super_admin'),
                            TextInput::make('guard_name')
                                ->label('Guard Name')
                                ->default('web')
                                ->disabled(),
                        ])
                        ->columns(2),

                    Section::make('Permissions')
                        ->schema($permissionSections)
                        ->visible(fn (?Role $record) => $record?->name !== 'super_admin'),

                    Section::make('ℹ️ Super Admin')
                        ->schema([
                            Forms\Components\Placeholder::make('info')
                                ->label('')
                                ->content('Super admin bypasses all permissions via Gate::before.'),
                        ])
                        ->visible(fn (?Role $record) => $record?->name === 'super_admin'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('guard_name'),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label('Permissions'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'view' => Pages\ViewRole::route('/{record}'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->can('manage_roles') ?? false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view_any_roles') ?? false;
    }

    public static function canView($record): bool
    {
        $user = auth()->user();
        if ($user?->hasRole('super_admin')) {
            return true;
        }
        return $user?->can('view_role', $record) ?? false;
    }

    public static function canEdit($record): bool
    {
        $user = auth()->user();
        if ($user?->hasRole('super_admin')) {
            return true;
        }
        if ($record?->name === 'super_admin' && !$user?->hasRole('super_admin')) {
            return false;
        }
        return $user?->can('update_role', $record) ?? false;
    }
}
