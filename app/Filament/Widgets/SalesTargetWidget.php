<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class SalesTargetWidget extends ChartWidget
{
    protected static ?string $heading = 'Online Sales Target';
    
    protected static ?int $sort = 4;
    
    protected int | string | array $columnSpan = 'full';
    
    protected function getData(): array
    {
        $target = 4500000; // 4500K target
        $current = Order::whereYear('created_at', now()->year)
            ->sum('amount') ?? 0;
        
        // Simulate online sales as 35% of total
        $onlineSales = $current * 0.35;
        $percentage = min(100, ($onlineSales / $target) * 100);
        
        return [
            'datasets' => [
                [
                    'label' => 'Sales Target',
                    'data' => [$percentage, 100 - $percentage],
                    'backgroundColor' => [
                        'rgb(59, 130, 246)',
                        'rgb(229, 231, 235)',
                    ],
                ],
            ],
            'labels' => ['Completed', 'Remaining'],
        ];
    }
    
    protected function getType(): string
    {
        return 'doughnut';
    }
    
    protected function getOptions(): array
    {
        $target = 4500000;
        $current = Order::whereYear('created_at', now()->year)
            ->sum('amount') ?? 0;
        $onlineSales = $current * 0.35;
        $percentage = min(100, ($onlineSales / $target) * 100);
        
        $previousYear = Order::whereYear('created_at', now()->subYear()->year)
            ->sum('amount') * 0.35 ?? 0;
        $change = $previousYear > 0 
            ? (($onlineSales - $previousYear) / $previousYear) * 100 
            : 0;
        
        return [
            'maintainAspectRatio' => false,
            'responsive' => true,
            'cutout' => '85%', // Thinner circle = more professional look
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'tooltip' => [
                    'enabled' => false,
                ],
            ],
            'elements' => [
                'arc' => [
                    'borderWidth' => 0, // Remove the white borders between segments
                    'borderRadius' => 10, // Round the edges of the progress bar
                ],
            ],
        ];
    }
    
    public function getDescription(): ?string
    {
        $target = 4500000;
        $current = Order::whereYear('created_at', now()->year)
            ->sum('amount') ?? 0;
        $onlineSales = $current * 0.35;
        
        $previousYear = Order::whereYear('created_at', now()->subYear()->year)
            ->sum('amount') * 0.35 ?? 0;
        $change = $previousYear > 0 
            ? (($onlineSales - $previousYear) / $previousYear) * 100 
            : 0;
        
        $monthsRemaining = 12 - now()->month;
        
        return number_format($onlineSales / 1000, 0) . 'K / ' . number_format($target / 1000, 0) . 'K • ▲ ' . 
               number_format(abs($change), 2) . '% • ' . $monthsRemaining . ' months left';
    }
}
