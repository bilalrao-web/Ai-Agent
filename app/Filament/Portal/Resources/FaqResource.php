<?php

namespace App\Filament\Portal\Resources;

use App\Filament\Portal\Resources\FaqResource\Pages;
use App\Models\Faq;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?string $navigationLabel = 'FAQs';
    protected static ?string $modelLabel = 'FAQ';
    protected static ?string $slug = 'faqs';
    protected static ?int $navigationSort = 4;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermissionTo('view_any_faqs') ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('is_active', true);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question')
                    ->searchable()
                    ->wrap()
                    ->limit(100),
                Tables\Columns\TextColumn::make('answer')
                    ->searchable()
                    ->wrap()
                    ->limit(200)
                    ->html(),
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
            'index' => Pages\ListFaqs::route('/'),
            'view' => Pages\ViewFaq::route('/{record}'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('question')
                    ->label('Question')
                    ->columnSpanFull(),
                TextEntry::make('answer')
                    ->label('Answer')
                    ->html()
                    ->columnSpanFull(),
                TextEntry::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Active' : 'Inactive')
                    ->color(fn ($state) => $state ? 'success' : 'danger'),
                TextEntry::make('created_at')
                    ->label('Created')
                    ->dateTime(),
            ]);
    }
}
