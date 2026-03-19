<?php

namespace App\Filament\Widgets;

use App\Models\CallLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class TotalCallsStatWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected int | string | array $columnSpan = 'full';
    
    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        $totalCalls = CallLog::count();
        $lastWeekCalls = CallLog::where('created_at', '>=', Carbon::now()->subWeek())->count();
        $previousWeekCalls = CallLog::whereBetween('created_at', [
            Carbon::now()->subWeeks(2),
            Carbon::now()->subWeek()
        ])->count();
        
        $change = $previousWeekCalls > 0 
            ? (($lastWeekCalls - $previousWeekCalls) / $previousWeekCalls) * 100 
            : 0;
        
        
        $sparklineData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $sparklineData[] = CallLog::whereDate('created_at', $date)->count();
        }

        return [
            Stat::make('Total Calls', number_format($totalCalls))
                ->description($change >= 0 ? '↑ ' . number_format(abs($change), 1) . '%' : '↓ ' . number_format(abs($change), 1) . '%')
                ->descriptionIcon($change >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($change >= 0 ? 'success' : 'danger')
                ->chart($sparklineData)
                ->extraAttributes([
                    'class' => 'glassmorphism-widget',
                ]),
        ];
    }
}
