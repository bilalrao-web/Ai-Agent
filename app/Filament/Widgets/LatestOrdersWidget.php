<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrdersWidget extends BaseWidget
{
    protected static ?string $heading = 'Latest Invoices';
    
    protected static ?int $sort = 5;
    
    protected int | string | array $columnSpan = 'full';
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->with('customer')
                    ->latest()
                    ->limit(3)
            )
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Invoice No.')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.email')
                    ->label('Email Address')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('id')
                    ->label('Product ID')
                    ->formatStateUsing(fn ($state) => str_pad($state, 6, '0', STR_PAD_LEFT)),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'delivered' => 'success',
                        'shipped' => 'info',
                        'processing' => 'warning',
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'delivered' => 'Paid',
                        'shipped' => 'Paid',
                        'processing' => 'Pending',
                        'pending' => 'Pending',
                        'cancelled' => 'Cancelled',
                        default => $state,
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false);
    }
    
    public function getHeading(): string
    {
        return 'Latest Invoices';
    }
    
    protected function getTableHeadingActions(): array
    {
        return [
            \Filament\Tables\Actions\Action::make('viewAll')
                ->label('View All')
                ->url(\App\Filament\Resources\OrderResource::getUrl('index'))
                ->icon('heroicon-o-arrow-right')
                ->color('primary'),
        ];
    }
}
