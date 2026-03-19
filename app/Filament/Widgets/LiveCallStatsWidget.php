<?php

namespace App\Filament\Widgets;

use App\Models\CallLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class LiveCallStatsWidget extends BaseWidget
{
    protected static ?int $sort = 8;
    
    protected int | string | array $columnSpan = 'full';
    
    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        $today = Carbon::today();
        
        $activeCalls = CallLog::where('status', 'active')->count();
        $callsToday = CallLog::whereDate('created_at', $today)->count();
        $avgDuration = CallLog::whereDate('created_at', $today)->avg('duration') ?? 0;
        $avgDurationMinutes = round($avgDuration / 60, 1);
        $totalToday = CallLog::whereDate('created_at', $today)->count();
        $completedToday = CallLog::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->count();
        $successRate = $totalToday > 0 ? round(($completedToday / $totalToday) * 100, 1) : 0;
        
        $activeSparkline = [];
        $callsSparkline = [];
        $durationSparkline = [];
        $successSparkline = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $activeSparkline[] = CallLog::where('status', 'active')
                ->whereDate('created_at', $date)
                ->count();
            $callsSparkline[] = CallLog::whereDate('created_at', $date)->count();
            $avg = CallLog::whereDate('created_at', $date)->avg('duration') ?? 0;
            $durationSparkline[] = round($avg / 60, 1);
            $dayTotal = CallLog::whereDate('created_at', $date)->count();
            $dayCompleted = CallLog::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->count();
            $successSparkline[] = $dayTotal > 0 ? round(($dayCompleted / $dayTotal) * 100, 1) : 0;
        }

        return [
            Stat::make('Active Calls Right Now', number_format($activeCalls))
                ->description('Live')
                ->descriptionIcon('heroicon-m-phone')
                ->color('success')
                ->chart($activeSparkline)
                ->extraAttributes([
                    'class' => 'glassmorphism-widget',
                ]),
            Stat::make('Calls Today', number_format($callsToday))
                ->description('Total')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info')
                ->chart($callsSparkline)
                ->extraAttributes([
                    'class' => 'glassmorphism-widget',
                ]),
            Stat::make('Avg Call Duration Today', number_format($avgDurationMinutes, 1) . ' min')
                ->description('Average')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart($durationSparkline)
                ->extraAttributes([
                    'class' => 'glassmorphism-widget',
                ]),
            Stat::make('Success Rate Today', number_format($successRate, 1) . '%')
                ->description('Completed')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($successRate >= 80 ? 'success' : ($successRate >= 50 ? 'warning' : 'danger'))
                ->chart($successSparkline)
                ->extraAttributes([
                    'class' => 'glassmorphism-widget',
                ]),
        ];
    }
}
