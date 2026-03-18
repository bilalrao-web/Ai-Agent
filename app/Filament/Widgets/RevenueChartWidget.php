<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Revenue Trend';
    
    protected static ?int $sort = 4;
    
    protected int | string | array $columnSpan = 5;

    protected function getData(): array
    {
        $labels = [];
        $revenueData = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('M d');
            
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();
            
            $revenueData[] = Order::whereBetween('created_at', [$dayStart, $dayEnd])->sum('amount');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Daily Revenue',
                    'data' => $revenueData,
                    'borderColor' => '#00d4ff',
                    'backgroundColor' => 'rgba(0, 212, 255, 0.2)',
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
