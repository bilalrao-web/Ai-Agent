<?php

namespace App\Filament\Widgets;

use App\Models\CallLog;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class CallSourceWidget extends ChartWidget
{
    protected static ?string $heading = 'Call Sources Breakdown';
    
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 5;

    protected function getData(): array
    {
        $totalCalls = CallLog::count();
        $incoming = CallLog::where('status', '!=', 'completed')->count();
        $completed = CallLog::where('status', 'completed')->count();
        $escalated = CallLog::where('escalated', true)->count();
        $direct = max(0, $totalCalls - $incoming - $completed - $escalated);

        return [
            'datasets' => [
                [
                    'label' => 'Calls by Source',
                    'data' => [
                        $incoming > 0 ? ($incoming / max($totalCalls, 1)) * 100 : 0,
                        $completed > 0 ? ($completed / max($totalCalls, 1)) * 100 : 0,
                        $escalated > 0 ? ($escalated / max($totalCalls, 1)) * 100 : 0,
                        $direct > 0 ? ($direct / max($totalCalls, 1)) * 100 : 0,
                    ],
                    'backgroundColor' => [
                        'rgba(0, 212, 255, 0.8)',
                        'rgba(123, 97, 255, 0.8)',
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(255, 159, 64, 0.8)',
                    ],
                    'borderColor' => [
                        'rgb(0, 212, 255)',
                        'rgb(123, 97, 255)',
                        'rgb(255, 99, 132)',
                        'rgb(255, 159, 64)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Incoming', 'Completed', 'Escalated', 'Direct'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
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
