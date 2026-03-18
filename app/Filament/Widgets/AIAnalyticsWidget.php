<?php

namespace App\Filament\Widgets;

use App\Models\CallLog;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class AIAnalyticsWidget extends ChartWidget
{
    protected static ?string $heading = 'AI Analytics - Call Traffic';
    
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 7;

    protected function getData(): array
    {
        $labels = [];
        $incomingData = [];
        $outgoingData = [];
        $escalatedData = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('M d');
            
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();
            
            $incomingData[] = CallLog::whereBetween('created_at', [$dayStart, $dayEnd])
                ->where('status', '!=', 'completed')
                ->count();
            
            $outgoingData[] = CallLog::whereBetween('created_at', [$dayStart, $dayEnd])
                ->where('status', 'completed')
                ->count();
            
            $escalatedData[] = CallLog::whereBetween('created_at', [$dayStart, $dayEnd])
                ->where('escalated', true)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Incoming Calls',
                    'data' => $incomingData,
                    'borderColor' => '#00d4ff',
                    'backgroundColor' => 'rgba(0, 212, 255, 0.2)',
                    'borderWidth' => 3,
                    'fill' => true,
                    'tension' => 0.45,
                    'pointRadius' => 0,
                    'pointHoverRadius' => 4,
                ],
                [
                    'label' => 'Completed Calls',
                    'data' => $outgoingData,
                    'borderColor' => '#7b61ff',
                    'backgroundColor' => 'rgba(123, 97, 255, 0.2)',
                    'borderWidth' => 3,
                    'fill' => true,
                    'tension' => 0.45,
                    'pointRadius' => 0,
                    'pointHoverRadius' => 4,
                ],
                [
                    'label' => 'Escalated Calls',
                    'data' => $escalatedData,
                    'borderColor' => '#ff6384',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderWidth' => 3,
                    'fill' => true,
                    'tension' => 0.45,
                    'pointRadius' => 0,
                    'pointHoverRadius' => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'color' => 'rgba(255,255,255,0.7)',
                        'padding' => 8,
                        'font' => ['size' => 11],
                        'usePointStyle' => true,
                    ],
                ],
            ],
            'scales' => [
                'x' => ['ticks' => ['color' => 'rgba(255,255,255,0.5)', 'font' => ['size' => 10]]],
                'y' => ['ticks' => ['color' => 'rgba(255,255,255,0.5)', 'font' => ['size' => 10]]],
            ],
        ];
    }

    protected function getExtraAttributes(): array
    {
        return [
            'class' => 'glassmorphism-widget',
        ];
    }
}
