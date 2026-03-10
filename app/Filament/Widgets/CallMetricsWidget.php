<?php

namespace App\Filament\Widgets;

use App\Models\CallLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class CallMetricsWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    
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
        $callsChange = $previousWeekCalls > 0 
            ? (($lastWeekCalls - $previousWeekCalls) / $previousWeekCalls) * 100 
            : 0;
        $callsSparkline = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $callsSparkline[] = CallLog::whereDate('created_at', $date)->count();
        }

        $totalDuration = CallLog::sum('duration');
        $lastWeekDuration = CallLog::where('created_at', '>=', Carbon::now()->subWeek())->sum('duration');
        $previousWeekDuration = CallLog::whereBetween('created_at', [
            Carbon::now()->subWeeks(2),
            Carbon::now()->subWeek()
        ])->sum('duration');
        $durationChange = $previousWeekDuration > 0 
            ? (($lastWeekDuration - $previousWeekDuration) / $previousWeekDuration) * 100 
            : 0;
        $durationSparkline = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $durationSparkline[] = CallLog::whereDate('created_at', $date)->sum('duration') / 60;
        }
        $hours = floor($totalDuration / 3600);
        $minutes = floor(($totalDuration % 3600) / 60);
        $formattedDuration = $hours . 'h ' . $minutes . 'm';

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
        $activeChange = $previousWeekActive > 0 
            ? (($lastWeekActive - $previousWeekActive) / $previousWeekActive) * 100 
            : 0;
        $activeSparkline = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $activeSparkline[] = CallLog::where('status', 'in-progress')
                ->whereDate('created_at', $date)
                ->count();
        }

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
        $escalationChange = $previousWeekRate > 0 
            ? $lastWeekRate - $previousWeekRate 
            : 0;
        $escalationSparkline = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $dayTotal = CallLog::whereDate('created_at', $date)->count();
            $dayEscalated = CallLog::where('escalated', true)
                ->whereDate('created_at', $date)
                ->count();
            $escalationSparkline[] = $dayTotal > 0 ? ($dayEscalated / $dayTotal) * 100 : 0;
        }

        return [
            Stat::make('Total Calls', number_format($totalCalls))
                ->description($callsChange >= 0 ? '↑ ' . number_format(abs($callsChange), 1) . '%' : '↓ ' . number_format(abs($callsChange), 1) . '%')
                ->descriptionIcon($callsChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($callsChange >= 0 ? 'success' : 'danger')
                ->chart($callsSparkline)
                ->extraAttributes([
                    'class' => 'glassmorphism-widget',
                ]),
            Stat::make('Total Call Duration', $formattedDuration)
                ->description($durationChange >= 0 ? '↑ ' . number_format(abs($durationChange), 1) . '%' : '↓ ' . number_format(abs($durationChange), 1) . '%')
                ->descriptionIcon($durationChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($durationChange >= 0 ? 'success' : 'danger')
                ->chart($durationSparkline)
                ->extraAttributes([
                    'class' => 'glassmorphism-widget',
                ]),
            Stat::make('Active Calls', number_format($activeCalls))
                ->description($activeChange >= 0 ? '↑ ' . number_format(abs($activeChange), 1) . '%' : '↓ ' . number_format(abs($activeChange), 1) . '%')
                ->descriptionIcon($activeChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($activeChange >= 0 ? 'success' : 'danger')
                ->chart($activeSparkline)
                ->extraAttributes([
                    'class' => 'glassmorphism-widget',
                ]),
            Stat::make('Escalation Rate', number_format($escalationRate, 1) . '%')
                ->description($escalationChange >= 0 ? '↑ ' . number_format(abs($escalationChange), 1) . '%' : '↓ ' . number_format(abs($escalationChange), 1) . '%')
                ->descriptionIcon($escalationChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($escalationChange >= 0 ? 'warning' : 'success')
                ->chart($escalationSparkline)
                ->extraAttributes([
                    'class' => 'glassmorphism-widget',
                ]),
        ];
    }
}
