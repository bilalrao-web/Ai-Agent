<?php

namespace App\Filament\Widgets;

use App\Models\CallLog;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class LiveMonitoringWidget extends BaseWidget
{
    protected static ?string $heading = 'Top Active Calls';
    
    protected static ?int $sort = 7;
    
    protected int | string | array $columnSpan = 6;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                CallLog::query()
                    ->where('status', 'in-progress')
                    ->orWhere('created_at', '>=', Carbon::now()->subMinutes(5))
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Call ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('simulated_query')
                    ->label('Query')
                    ->limit(50)
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('duration')
                    ->label('Duration')
                    ->formatStateUsing(fn ($state) => gmdate('i:s', $state))
                    ->suffix(' min')
                    ->sortable(),
                Tables\Columns\IconColumn::make('status')
                    ->label('Status')
                    ->icon(fn (string $state): string => match ($state) {
                        'in-progress' => 'heroicon-o-signal',
                        'completed' => 'heroicon-o-check-circle',
                        default => 'heroicon-o-clock',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'in-progress' => 'success',
                        'completed' => 'gray',
                        default => 'warning',
                    }),
                Tables\Columns\IconColumn::make('escalated')
                    ->label('Escalated')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Started')
                    ->dateTime('H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('5s');
    }

    protected function getExtraAttributes(): array
    {
        return [
            'class' => 'glassmorphism-widget',
        ];
    }
}
