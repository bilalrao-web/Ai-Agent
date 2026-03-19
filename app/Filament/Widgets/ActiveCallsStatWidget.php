<?php

namespace App\Filament\Widgets;

use App\Models\CallLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class ActiveCallsStatWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';
    
    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        $activeCalls = CallLog::where('status', 'in-progress')->count();
        $lastWeekActive = CallLog::where('status', 'in-progress')
            ->where('created_at', '>=', Carbon::now()->subWeek())
            ->count();
        $previousWeekActive = CallLog::where('status', 'in-progress')
            ->whereBetween('created_at', [
                Carbon::now()->subWeeks(2),
                Carbon::now()->subWeek()
            ])
            ->count();
        
        $change = $previousWeekActive > 0 
            ? (($lastWeekActive - $previousWeekActive) / $previousWeekActive) * 100 
            : 0;
        
        
        $sparklineData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $sparklineData[] = CallLog::where('status', 'in-progress')
                ->whereDate('created_at', $date)
                ->count();
        }

        return [
            Stat::make('Active Calls', number_format($activeCalls))
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
