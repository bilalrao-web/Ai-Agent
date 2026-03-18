<?php

namespace App\Filament\Widgets;

use App\Models\CallLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class CallPerformanceWidget extends BaseWidget
{
    protected static ?int $sort = 9;
    
    protected int | string | array $columnSpan = 6;

    protected function getStats(): array
    {
        $totalCalls = CallLog::count();
        $avgDuration = CallLog::avg('duration') ?? 0;
        $escalationRate = $totalCalls > 0 
            ? (CallLog::where('escalated', true)->count() / $totalCalls) * 100 
            : 0;
        
        $targetCalls = 500;
        $targetDuration = 300;
        $targetEscalation = 10;

        $callsProgress = $totalCalls > 0 ? min(100, ($totalCalls / $targetCalls) * 100) : 0;
        $durationProgress = $avgDuration > 0 ? min(100, ($avgDuration / $targetDuration) * 100) : 0;
        $escalationProgress = $escalationRate > 0 ? min(100, ($escalationRate / $targetEscalation) * 100) : 0;

        return [
            Stat::make('Call Volume Target', number_format($totalCalls) . ' / ' . number_format($targetCalls))
                ->description(number_format($callsProgress, 1) . '% of target')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($callsProgress >= 80 ? 'success' : ($callsProgress >= 50 ? 'warning' : 'danger'))
                ->chart([$callsProgress]),
            Stat::make('Avg Call Duration', gmdate('i:s', $avgDuration))
                ->description(number_format($durationProgress, 1) . '% of target (5 min)')
                ->descriptionIcon('heroicon-m-clock')
                ->color($durationProgress <= 100 ? 'success' : 'warning')
                ->chart([$durationProgress]),
            Stat::make('Escalation Rate', number_format($escalationRate, 1) . '%')
                ->description(number_format($escalationProgress, 1) . '% of threshold')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color($escalationRate <= 10 ? 'success' : 'danger')
                ->chart([$escalationProgress]),
        ];
    }
}
