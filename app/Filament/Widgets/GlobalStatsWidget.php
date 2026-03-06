<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class GlobalStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalCustomers = Customer::count();
        $custChange = $this->getPercentageChange(Customer::class);
        
        $totalRevenue = Order::sum('amount') ?? 0;
        $revChange = $this->getPercentageChange(Order::class, 'sum', 'amount');
        
        $totalSales = Order::count();
        $salesChange = $this->getPercentageChange(Order::class);
        
        $activeTickets = Ticket::whereIn('status', ['open', 'in_progress'])->count();
        $ticketChange = $this->getTicketPercentageChange();

        return [
            Stat::make('Total Customer', Number::format($totalCustomers))
                ->description($custChange['label'])
                ->descriptionIcon($custChange['icon'])
                ->color($custChange['color'])
                ->chart($this->getChartData(Customer::class)),

            Stat::make('Total Revenue', Number::currency($totalRevenue, 'USD'))
                ->description($revChange['label'])
                ->descriptionIcon($revChange['icon'])
                ->color($revChange['color'])
                ->chart($this->getChartData(Order::class, 'sum', 'amount')),
                
            Stat::make('Total Sales', Number::format($totalSales))
                ->description($salesChange['label'])
                ->descriptionIcon($salesChange['icon'])
                ->color($salesChange['color'])
                ->chart($this->getChartData(Order::class)),
                
            Stat::make('Active Tickets', Number::format($activeTickets))
                ->description($ticketChange['label'])
                ->descriptionIcon($ticketChange['icon'])
                ->color($ticketChange['color']),
        ];
    }

    private function getPercentageChange($model, $type = 'count', $column = null)
    {
        $current = $type === 'count' 
            ? $model::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count() 
            : $model::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum($column) ?? 0;
                
        $prev = $type === 'count' 
            ? $model::whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->count() 
            : $model::whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->sum($column) ?? 0;
        
        $change = $prev > 0 ? (($current - $prev) / $prev) * 100 : ($current > 0 ? 100 : 0);
        $isPositive = $change >= 0;

        return [
            'label' => number_format(abs($change), 2) . '% from last month',
            'icon' => $isPositive ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down',
            'color' => $isPositive ? 'success' : 'danger',
        ];
    }
    
    private function getTicketPercentageChange()
    {
        $current = Ticket::whereIn('status', ['open', 'in_progress'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
            
        $prev = Ticket::whereIn('status', ['open', 'in_progress'])
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        
        $change = $prev > 0 ? (($current - $prev) / $prev) * 100 : ($current > 0 ? 100 : 0);
        $isPositive = $change <= 0;

        return [
            'label' => number_format(abs($change), 2) . '% from last month',
            'icon' => $isPositive ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-arrow-trending-up',
            'color' => $isPositive ? 'success' : 'warning',
        ];
    }

    protected function getChartData($model, $type = 'count', $column = null): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $data[] = $type === 'count' 
                ? $model::whereDate('created_at', $date->format('Y-m-d'))->count() 
                : ($model::whereDate('created_at', $date->format('Y-m-d'))->sum($column) ?? 0);
        }
        return $data;
    }
}
