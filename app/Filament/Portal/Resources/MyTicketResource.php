<?php

namespace App\Filament\Portal\Resources;

use App\Filament\Portal\Resources\MyTicketResource\Pages;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MyTicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationLabel = 'My Tickets';
    protected static ?string $modelLabel = 'Ticket';
    protected static ?string $slug = 'my-tickets';

    public static function getEloquentQuery(): Builder
    {
        $customerId = auth()->user()?->customer?->id ?? 0;
        return parent::getEloquentQuery()->where('customer_id', $customerId);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('issue_type')->required()->maxLength(255),
                Forms\Components\Textarea::make('description')->required()->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('issue_type')->searchable(),
                Tables\Columns\TextColumn::make('status')->badge()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyTickets::route('/'),
            'create' => Pages\CreateMyTicket::route('/create'),
            'view' => Pages\ViewMyTicket::route('/{record}'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermissionTo('view_any_tickets') ?? false;
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('issue_type'),
                TextEntry::make('description')->columnSpanFull(),
                TextEntry::make('status')->badge(),
                TextEntry::make('created_at')->dateTime(),
            ]);
    }
}
