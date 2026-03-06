<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class WeeklySummaryChart extends ChartWidget
{
    protected static ?string $heading = 'Weekly Summary';
    
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];
    
    protected function getData(): array
    {
        $data = [];
        $labels = [];
        
        // Get last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayName = $date->format('l'); // Monday, Tuesday, etc.
            $labels[] = substr($dayName, 0, 3); // Mon, Tue, etc.
            
            $data[] = Order::whereDate('created_at', $date->format('Y-m-d'))
                ->sum('amount') ?? 0;
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $data,
                    'fill' => 'start', // This makes it an area chart
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)', // Light blue tint
                    'borderColor' => '#3b82f6',
                    'borderWidth' => 3,
                    'pointRadius' => 4,
                    'pointBackgroundColor' => '#3b82f6',
                    'pointBorderColor' => '#ffffff',
                    'pointBorderWidth' => 2,
                    'pointHoverRadius' => 6,
                    'tension' => 0.4, // This creates the smooth "wave" look
                ],
            ],
            'labels' => $labels,
        ];
    }
    
    protected function getType(): string
    {
        return 'line'; // Change 'bar' to 'line' for a modern feel
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
                        'callback' => 'function(value) { return "$" + value.toLocaleString(); }',
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
                    'display' => false,
                ],
            ],
        ];
    }
}
