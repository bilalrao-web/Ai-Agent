<?php

namespace App\Filament\Widgets;

use App\Models\CallLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class EscalationRateWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    
    protected int | string | array $columnSpan = 'full';
    
    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        $totalCalls = CallLog::count();
        $escalatedCalls = CallLog::where('escalated', true)->count();
        $escalationRate = $totalCalls > 0 ? ($escalatedCalls / $totalCalls) * 100 : 0;
        
        $lastWeekTotal = CallLog::where('created_at', '>=', Carbon::now()->subWeek())->count();
        $lastWeekEscalated = CallLog::where('escalated', true)
            ->where('created_at', '>=', Carbon::now()->subWeek())
            ->count();
        $lastWeekRate = $lastWeekTotal > 0 ? ($lastWeekEscalated / $lastWeekTotal) * 100 : 0;
        
        $previousWeekTotal = CallLog::whereBetween('created_at', [
            Carbon::now()->subWeeks(2),
            Carbon::now()->subWeek()
        ])->count();
        $previousWeekEscalated = CallLog::where('escalated', true)
            ->whereBetween('created_at', [
                Carbon::now()->subWeeks(2),
                Carbon::now()->subWeek()
            ])
            ->count();
        $previousWeekRate = $previousWeekTotal > 0 ? ($previousWeekEscalated / $previousWeekTotal) * 100 : 0;
        
        $change = $previousWeekRate > 0 
            ? $lastWeekRate - $previousWeekRate 
            : 0;
        
        $sparklineData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $dayTotal = CallLog::whereDate('created_at', $date)->count();
            $dayEscalated = CallLog::where('escalated', true)
                ->whereDate('created_at', $date)
                ->count();
            $sparklineData[] = $dayTotal > 0 ? ($dayEscalated / $dayTotal) * 100 : 0;
        }

        return [
            Stat::make('Escalation Rate', number_format($escalationRate, 1) . '%')
                ->description($change >= 0 ? '↑ ' . number_format(abs($change), 1) . '%' : '↓ ' . number_format(abs($change), 1) . '%')
                ->descriptionIcon($change >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($change >= 0 ? 'warning' : 'success')
                ->chart($sparklineData)
                ->extraAttributes([
                    'class' => 'glassmorphism-widget',
                ]),
        ];
    }
}
