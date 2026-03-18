<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Filament\Traits\ChartOptionsTrait;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChartWidget extends ChartWidget
{
    use ChartOptionsTrait;

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
        $textColor = $this->getChartTextColor();
        $mutedColor = $this->getChartMutedColor();
        $gridColor = $this->getChartGridColor();

        return [
            'maintainAspectRatio' => false,
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'color' => $textColor,
                        'padding' => 16,
                        'font' => ['size' => 11, 'family' => "'DM Sans', sans-serif"],
                        'usePointStyle' => true,
                    ],
                ],
                'tooltip' => $this->getChartTooltipOptions(),
            ],
            'scales' => [
                'x' => [
                    'ticks' => [
                        'color' => $mutedColor,
                        'font' => ['size' => 10],
                    ],
                    'grid' => [
                        'color' => $gridColor,
                    ],
                ],
                'y' => [
                    'ticks' => [
                        'color' => $mutedColor,
                        'font' => ['size' => 10],
                    ],
                    'grid' => [
                        'color' => $gridColor,
                    ],
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
