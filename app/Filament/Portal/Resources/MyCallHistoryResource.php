<?php

namespace App\Filament\Portal\Resources;

use App\Filament\Portal\Resources\MyCallHistoryResource\Pages;
use App\Models\CallLog;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MyCallHistoryResource extends Resource
{
    protected static ?string $model = CallLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone';
    protected static ?string $navigationLabel = 'My Call History';
    protected static ?string $modelLabel = 'Call';
    protected static ?string $slug = 'my-call-history';

    public static function getEloquentQuery(): Builder
    {
        $customerId = auth()->user()?->customer?->id ?? 0;
        return parent::getEloquentQuery()->where('customer_id', $customerId);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('simulated_query')->limit(50)->placeholder('—'),
                Tables\Columns\TextColumn::make('duration')->suffix(' sec'),
                Tables\Columns\TextColumn::make('status')->badge(),
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
            'index' => Pages\ListMyCallHistory::route('/'),
            'view' => Pages\ViewMyCallHistory::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('simulated_query')->label('Query')->columnSpanFull(),
                TextEntry::make('duration')->suffix(' sec'),
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
