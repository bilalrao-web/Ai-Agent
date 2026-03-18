<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class TotalRevenueWidget extends BaseWidget
{
    protected static ?int $sort = 10;
    
    protected int | string | array $columnSpan = 'full';
    
    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        $totalRevenue = Order::sum('amount');
        $lastWeekRevenue = Order::where('created_at', '>=', Carbon::now()->subWeek())->sum('amount');
        $previousWeekRevenue = Order::whereBetween('created_at', [
            Carbon::now()->subWeeks(2),
            Carbon::now()->subWeek()
        ])->sum('amount');
        
        $change = $previousWeekRevenue > 0 
            ? (($lastWeekRevenue - $previousWeekRevenue) / $previousWeekRevenue) * 100 
            : 0;
        
        
        $sparklineData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $sparklineData[] = Order::whereDate('created_at', $date)->sum('amount');
        }

        return [
            Stat::make('Total Revenue', '$' . number_format($totalRevenue, 2))
                ->description($change >= 0 ? '↑ ' . number_format(abs($change), 1) . '%' : '↓ ' . number_format(abs($change), 1) . '%')
                ->descriptionIcon($change >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($change >= 0 ? 'success' : 'danger')
                ->chart($sparklineData)
                ->extraAttributes([
                    'class' => 'glassmorphism-widget',
                ]),
        ];
    }
}
