<?php

namespace App\Filament\Widgets;

use App\Models\CallLog;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class CallStatusWidget extends ChartWidget
{
    protected static ?string $heading = 'Call Status Distribution';
    
    protected static ?int $sort = 7;
    
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $inProgress = CallLog::where('status', 'in-progress')->count();
        $completed = CallLog::where('status', 'completed')->count();
        $failed = CallLog::where('status', 'failed')->count();
        $other = CallLog::whereNotIn('status', ['in-progress', 'completed', 'failed'])->count();

        return [
            'datasets' => [
                [
                    'label' => 'Status',
                    'data' => [$inProgress, $completed, $failed, $other],
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(156, 163, 175, 0.8)',
                    ],
                    'borderColor' => [
                        'rgb(34, 197, 94)',
                        'rgb(59, 130, 246)',
                        'rgb(239, 68, 68)',
                        'rgb(156, 163, 175)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['In Progress', 'Completed', 'Failed', 'Other'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'color' => 'rgba(255, 255, 255, 0.8)',
                        'font' => [
                            'family' => "'DM Sans', sans-serif",
                        ],
                        'padding' => 15,
                        'usePointStyle' => true,
                    ],
                ],
                'tooltip' => [
                    'backgroundColor' => 'rgba(17, 24, 39, 0.9)',
                    'titleColor' => 'rgba(255, 255, 255, 0.9)',
                    'bodyColor' => 'rgba(255, 255, 255, 0.7)',
                    'borderColor' => 'rgba(255, 255, 255, 0.1)',
                    'borderWidth' => 1,
                ],
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
