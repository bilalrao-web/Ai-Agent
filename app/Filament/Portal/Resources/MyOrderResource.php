<?php

namespace App\Filament\Portal\Resources;

use App\Filament\Portal\Resources\MyOrderResource\Pages;
use App\Models\Order;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MyOrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'My Orders';
    protected static ?string $modelLabel = 'Order';
    protected static ?string $slug = 'my-orders';
    protected static ?string $title = 'My Orders';

    public static function getEloquentQuery(): Builder
    {
        $customer = auth()->user()?->customer;
        if (! $customer) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }
        return Order::query()->where('customer_id', $customer->id);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()->sortable(),
                Tables\Columns\TextColumn::make('delivery_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('amount')->money()->sortable(),
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
            'index' => Pages\ListMyOrders::route('/'),
            'view' => Pages\ViewMyOrder::route('/{record}'),
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
                TextEntry::make('order_number'),
                TextEntry::make('status')->badge(),
                TextEntry::make('delivery_date')->date(),
                TextEntry::make('amount')->money(),
                TextEntry::make('created_at')->dateTime(),
            ]);
    }
}
