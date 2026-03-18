<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class TotalCustomersWidget extends BaseWidget
{
    protected static ?int $sort = 12;
    
    protected int | string | array $columnSpan = 'full';
    
    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        $totalCustomers = Customer::count();
        $lastWeekCustomers = Customer::where('created_at', '>=', Carbon::now()->subWeek())->count();
        $previousWeekCustomers = Customer::whereBetween('created_at', [
            Carbon::now()->subWeeks(2),
            Carbon::now()->subWeek()
        ])->count();
        
        $change = $previousWeekCustomers > 0 
            ? (($lastWeekCustomers - $previousWeekCustomers) / $previousWeekCustomers) * 100 
            : 0;
        
        
        $sparklineData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $sparklineData[] = Customer::whereDate('created_at', $date)->count();
        }

        return [
            Stat::make('Total Customers', number_format($totalCustomers))
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
