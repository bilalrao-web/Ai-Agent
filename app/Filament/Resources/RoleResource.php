<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Roles';
    protected static ?string $modelLabel = 'Role';
    protected static ?string $navigationGroup = 'Roles & Permissions';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Role Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Role Name')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Enter role name'),
                        Forms\Components\TextInput::make('guard_name')
                            ->label('Guard Name')
                            ->default('web')
                            ->required()
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Permissions')
                    ->description('Select permissions for this role. Portal module permissions (my_orders.*, my_tickets.*, my_call_history.*) control which modules are visible in the Customer Portal. If a module permission is not checked, that module will be hidden from the portal.')
                    ->schema([
                        Forms\Components\CheckboxList::make('permissions')
                            ->relationship('permissions', 'name')
                            ->searchable()
                            ->bulkToggleable()
                            ->gridDirection('row')
                            ->columns(4)
                            ->columnSpanFull()
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                $name = $record->name;
                                
                                // Portal module permissions - format nicely
                                if (str_starts_with($name, 'my_orders.') || 
                                    str_starts_with($name, 'my_tickets.') || 
                                    str_starts_with($name, 'my_call_history.')) {
                                    
                                    $parts = explode('.', $name);
                                    $module = $parts[0] ?? '';
                                    $action = $parts[1] ?? '';
                                    
                                    $moduleLabel = match($module) {
                                        'my_orders' => 'My Orders',
                                        'my_tickets' => 'My Tickets',
                                        'my_call_history' => 'My Call History',
                                        default => ucwords(str_replace('_', ' ', $module)),
                                    };
                                    
                                    $actionLabel = match($action) {
                                        'view-any' => 'View',
                                        'view' => 'View Details',
                                        'create' => 'Create',
                                        'edit' => 'Edit',
                                        'delete' => 'Delete',
                                        default => ucwords(str_replace('-', ' ', $action)),
                                    };
                                    
                                    return "{$moduleLabel} - {$actionLabel}";
                                }
                                
                                // Other permissions - format normally
                                return str_replace('_', ' ', ucwords($name, '_'));
                            }),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Role name copied!')
                    ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('guard_name')
                    ->label('Guard Name')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->label('Permissions')
                    ->counts('permissions')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d/M/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime('d/M/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('guard_name')
                    ->label('Guard Name')
                    ->options([
                        'web' => 'Web',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name', 'asc');
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
}
