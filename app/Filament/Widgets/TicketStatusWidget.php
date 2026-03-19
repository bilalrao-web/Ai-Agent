<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\ChartWidget;

class TicketStatusWidget extends ChartWidget
{
    protected static ?string $heading = 'Ticket Status Overview';
    
    protected static ?int $sort = 5;
    
    protected int | string | array $columnSpan = 7;

    protected function getData(): array
    {
        $open = Ticket::where('status', 'open')->count();
        $inProgress = Ticket::where('status', 'in_progress')->count();
        $resolved = Ticket::where('status', 'resolved')->count();
        $closed = Ticket::where('status', 'closed')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Tickets',
                    'data' => [$open, $inProgress, $resolved, $closed],
                    'backgroundColor' => [
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(251, 191, 36, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(156, 163, 175, 0.8)',
                    ],
                    'borderColor' => [
                        'rgb(239, 68, 68)',
                        'rgb(251, 191, 36)',
                        'rgb(34, 197, 94)',
                        'rgb(156, 163, 175)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Open', 'In Progress', 'Resolved', 'Closed'],
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
