<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CallLogResource\Pages;
use App\Models\CallLog;
use Filament\Forms\Form;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CallLogResource extends Resource
{
    protected static ?string $model = CallLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone';
    protected static ?string $navigationGroup = 'Data';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('customer.name')->label('Customer')->searchable()->placeholder('—'),
                Tables\Columns\TextColumn::make('simulated_query')->limit(40)->placeholder('—'),
                Tables\Columns\TextColumn::make('duration')->suffix(' sec'),
                Tables\Columns\IconColumn::make('escalated')->boolean(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCallLogs::route('/'),
            'view' => Pages\ViewCallLog::route('/{record}'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->can('viewAny', static::getModel()) ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('customer.name')->label('Customer'),
                TextEntry::make('simulated_query')->label('Simulated query')->columnSpanFull(),
                TextEntry::make('duration')->suffix(' sec'),
                TextEntry::make('escalated')->badge()->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
                TextEntry::make('status')->badge(),
                TextEntry::make('created_at')->dateTime(),
                RepeatableEntry::make('conversationMessages')
                    ->label('Conversation')
                    ->schema([
                        TextEntry::make('role')->badge(),
                        TextEntry::make('content')->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
