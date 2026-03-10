<?php

namespace App\Filament\Widgets;

use App\Models\CallLog;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\View\View;

class RecentCallsWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent AI Voice Calls';
    
    protected static ?int $sort = 8;
    
    protected int | string | array $columnSpan = 7;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                CallLog::query()
                    ->with('customer')
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Contact Name')
                    ->searchable()
                    ->placeholder('Anonymous')
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.phone')
                    ->label('Phone Number')
                    ->searchable()
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration')
                    ->label('Duration')
                    ->formatStateUsing(function ($state) {
                        $minutes = floor($state / 60);
                        $seconds = $state % 60;
                        return $minutes . 'm ' . $seconds . 's';
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'failed' => 'danger',
                        'escalated' => 'warning',
                        'voicemail' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('sentiment')
                    ->label('Sentiment')
                    ->badge()
                    ->formatStateUsing(function ($record) {
                        if ($record->escalated) {
                            return 'Negative';
                        }
                        if ($record->status === 'completed') {
                            return 'Positive';
                        }
                        return 'Neutral';
                    })
                    ->color(function ($record) {
                        if ($record->escalated) {
                            return 'danger';
                        }
                        if ($record->status === 'completed') {
                            return 'success';
                        }
                        return 'gray';
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Called At')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false)
            ->striped(false)
            ->searchable(false);
    }

    protected function getTableContentFooter(): ?View
    {
        return null;
    }

    protected function getExtraAttributes(): array
    {
        return [
            'class' => 'glassmorphism-widget',
        ];
    }
}
