<?php

namespace App\Filament\Widgets;

use App\Models\CallLog;
use Filament\Widgets\ChartWidget;

class CallOutcomeSummaryWidget extends ChartWidget
{
    protected static ?string $heading = 'Call Outcome Summary';
    
    protected static ?int $sort = 9;
    
    protected int | string | array $columnSpan = 5;

    protected function getData(): array
    {
        $completed = CallLog::where('status', 'completed')->count();
        $failed = CallLog::where('status', 'failed')->count();
        $escalated = CallLog::where('status', 'escalated')->count();
        $voicemail = CallLog::where('status', 'voicemail')->count();
        $noAnswer = CallLog::whereNotIn('status', ['completed', 'failed', 'escalated', 'voicemail', 'active', 'in-progress'])
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Call Outcomes',
                    'data' => [$completed, $failed, $escalated, $voicemail, $noAnswer],
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(107, 114, 128, 0.8)',
                    ],
                    'borderColor' => [
                        'rgb(34, 197, 94)',
                        'rgb(239, 68, 68)',
                        'rgb(245, 158, 11)',
                        'rgb(59, 130, 246)',
                        'rgb(107, 114, 128)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Completed', 'Failed', 'Escalated', 'Voicemail', 'No Answer'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'responsive' => true,
            'cutout' => '68%',
            'layout' => [
                'padding' => 20,
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'color' => 'rgba(255,255,255,0.7)',
                        'padding' => 20,
                        'font' => ['size' => 11],
                        'usePointStyle' => true,
                    ],
                ],
            ],
            'scales' => [
                'x' => ['display' => false],
                'y' => ['display' => false],
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
