<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class SalesHistoryChart extends ChartWidget
{
    protected static ?string $heading = 'Sales History';
    
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];
    
    protected function getData(): array
    {
        $data = [];
        $labels = [];
        
        // Get last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M'); // Jan, Feb, etc.
            
            // Simulate different sales channels (for demo purposes)
            // In real app, you'd have a channel field in orders
            $total = Order::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('amount') ?? 0;
            
            // Split into channels (example: 40% marketing, 35% online, 25% offline)
            $data['marketing'][] = $total * 0.4;
            $data['online'][] = $total * 0.35;
            $data['offline'][] = $total * 0.25;
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Marketing Sales',
                    'data' => $data['marketing'] ?? [],
                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
                [
                    'label' => 'Online Sales',
                    'data' => $data['online'] ?? [],
                    'backgroundColor' => 'rgba(96, 165, 250, 0.8)',
                    'borderColor' => 'rgb(96, 165, 250)',
                ],
                [
                    'label' => 'Offline Sales',
                    'data' => $data['offline'] ?? [],
                    'backgroundColor' => 'rgba(30, 64, 175, 0.8)',
                    'borderColor' => 'rgb(30, 64, 175)',
                ],
            ],
            'labels' => $labels,
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
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) { return "$" + (value / 1000) + "k"; }',
                        'font' => ['size' => 10],
                    ],
                ],
                'x' => [
                    'ticks' => [
                        'font' => ['size' => 10],
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                    'labels' => [
                        'font' => ['size' => 10],
                        'boxWidth' => 12,
                    ],
                ],
            ],
            'stacked' => true,
        ];
    }
}
