<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Filament\Traits\ChartOptionsTrait;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class OrderStatusWidget extends ChartWidget
{
    use ChartOptionsTrait;

    protected static ?string $heading = 'Order Status Distribution';
    
    protected static ?int $sort = 11;
    
    protected int | string | array $columnSpan = 5;

    protected function getData(): array
    {
        $pending = Order::where('status', 'pending')->count();
        $processing = Order::where('status', 'processing')->count();
        $shipped = Order::where('status', 'shipped')->count();
        $delivered = Order::where('status', 'delivered')->count();
        $cancelled = Order::where('status', 'cancelled')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => [$pending, $processing, $shipped, $delivered, $cancelled],
                    'backgroundColor' => [
                        'rgba(251, 191, 36, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                    ],
                    'borderColor' => [
                        'rgb(251, 191, 36)',
                        'rgb(59, 130, 246)',
                        'rgb(139, 92, 246)',
                        'rgb(34, 197, 94)',
                        'rgb(239, 68, 68)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        $textColor = $this->getChartTextColor();

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
                        'color' => $textColor,
                        'padding' => 16,
                        'font' => ['size' => 11, 'family' => "'DM Sans', sans-serif"],
                        'usePointStyle' => true,
                        'pointStyleWidth' => 8,
                    ],
                ],
                'tooltip' => $this->getChartTooltipOptions(),
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
