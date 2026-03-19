<?php

namespace App\Filament\Widgets;

use App\Models\CallLog;
use App\Models\Customer;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class TopCalledNumbersWidget extends BaseWidget
{
    protected static ?string $heading = 'Top Called Numbers';
    
    protected static ?int $sort = 10;
    
    protected int | string | array $columnSpan = 7;

    public function table(Table $table): Table
    {
        $stats = CallLog::query()
            ->select('customer_id', 
                DB::raw('COUNT(*) as total_calls'), 
                DB::raw('AVG(duration) as avg_duration'), 
                DB::raw('MAX(created_at) as last_called'))
            ->whereNotNull('customer_id')
            ->groupBy('customer_id')
            ->orderByDesc('total_calls')
            ->limit(5)
            ->get()
            ->keyBy('customer_id');

        $customerIds = $stats->pluck('customer_id')->toArray();
        
        return $table
            ->query(
                Customer::query()
                    ->whereIn('id', $customerIds)
                    ->with(['callLogs' => function ($query) {
                        $query->latest()->limit(1);
                    }])
            )
            ->columns([
                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone Number')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('total_calls')
                    ->label('Total Calls')
                    ->getStateUsing(function ($record) use ($stats) {
                        $stat = $stats->get($record->id);
                        return $stat ? number_format($stat->total_calls) : '0';
                    })
                    ->sortable(false),
                Tables\Columns\TextColumn::make('avg_duration')
                    ->label('Avg Duration')
                    ->getStateUsing(function ($record) use ($stats) {
                        $stat = $stats->get($record->id);
                        if (!$stat || !$stat->avg_duration) return '0m 0s';
                        $minutes = floor($stat->avg_duration / 60);
                        $seconds = round($stat->avg_duration % 60);
                        return $minutes . 'm ' . $seconds . 's';
                    })
                    ->sortable(false),
                Tables\Columns\TextColumn::make('last_called')
                    ->label('Last Called')
                    ->getStateUsing(function ($record) use ($stats) {
                        $stat = $stats->get($record->id);
                        return $stat && $stat->last_called ? \Carbon\Carbon::parse($stat->last_called)->diffForHumans() : '—';
                    })
                    ->sortable(false),
                Tables\Columns\TextColumn::make('last_status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        $lastCall = $record->callLogs->first();
                        return $lastCall ? ucfirst($lastCall->status) : '—';
                    })
                    ->color(function ($record) {
                        $lastCall = $record->callLogs->first();
                        if (!$lastCall) return 'gray';
                        return match ($lastCall->status) {
                            'completed' => 'success',
                            'failed' => 'danger',
                            'escalated' => 'warning',
                            'voicemail' => 'info',
                            default => 'gray',
                        };
                    }),
            ])
            ->paginated(false)
            ->searchable(false);
    }

    protected function getExtraAttributes(): array
    {
        return [
            'class' => 'glassmorphism-widget',
        ];
    }
}
