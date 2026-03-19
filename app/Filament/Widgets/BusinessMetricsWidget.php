<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class BusinessMetricsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
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
        $revenueChange = $previousWeekRevenue > 0 
            ? (($lastWeekRevenue - $previousWeekRevenue) / $previousWeekRevenue) * 100 
            : 0;
        $revenueSparkline = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $revenueSparkline[] = Order::whereDate('created_at', $date)->sum('amount');
        }

        $totalOrders = Order::count();
        $lastWeekOrders = Order::where('created_at', '>=', Carbon::now()->subWeek())->count();
        $previousWeekOrders = Order::whereBetween('created_at', [
            Carbon::now()->subWeeks(2),
            Carbon::now()->subWeek()
        ])->count();
        $ordersChange = $previousWeekOrders > 0 
            ? (($lastWeekOrders - $previousWeekOrders) / $previousWeekOrders) * 100 
            : 0;
        $ordersSparkline = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $ordersSparkline[] = Order::whereDate('created_at', $date)->count();
        }

        $totalCustomers = Customer::count();
        $lastWeekCustomers = Customer::where('created_at', '>=', Carbon::now()->subWeek())->count();
        $previousWeekCustomers = Customer::whereBetween('created_at', [
            Carbon::now()->subWeeks(2),
            Carbon::now()->subWeek()
        ])->count();
        $customersChange = $previousWeekCustomers > 0 
            ? (($lastWeekCustomers - $previousWeekCustomers) / $previousWeekCustomers) * 100 
            : 0;
        $customersSparkline = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $customersSparkline[] = Customer::whereDate('created_at', $date)->count();
        }

        $openTickets = Ticket::whereIn('status', ['open', 'in_progress'])->count();
        $lastWeekTickets = Ticket::whereIn('status', ['open', 'in_progress'])
            ->where('created_at', '>=', Carbon::now()->subWeek())
            ->count();
        $previousWeekTickets = Ticket::whereIn('status', ['open', 'in_progress'])
            ->whereBetween('created_at', [
                Carbon::now()->subWeeks(2),
                Carbon::now()->subWeek()
            ])
            ->count();
        $ticketsChange = $previousWeekTickets > 0 
            ? (($lastWeekTickets - $previousWeekTickets) / $previousWeekTickets) * 100 
            : 0;
        $ticketsSparkline = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $ticketsSparkline[] = Ticket::whereIn('status', ['open', 'in_progress'])
                ->whereDate('created_at', $date)
                ->count();
        }

        return [
            Stat::make('Total Revenue', '$' . number_format($totalRevenue, 2))
                ->description($revenueChange >= 0 ? '↑ ' . number_format(abs($revenueChange), 1) . '%' : '↓ ' . number_format(abs($revenueChange), 1) . '%')
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueChange >= 0 ? 'success' : 'danger')
                ->chart($revenueSparkline)
                ->extraAttributes([
                    'class' => 'glassmorphism-widget',
                ]),
            Stat::make('Total Orders', number_format($totalOrders))
                ->description($ordersChange >= 0 ? '↑ ' . number_format(abs($ordersChange), 1) . '%' : '↓ ' . number_format(abs($ordersChange), 1) . '%')
                ->descriptionIcon($ordersChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($ordersChange >= 0 ? 'success' : 'danger')
                ->chart($ordersSparkline)
                ->extraAttributes([
                    'class' => 'glassmorphism-widget',
                ]),
            Stat::make('Total Customers', number_format($totalCustomers))
                ->description($customersChange >= 0 ? '↑ ' . number_format(abs($customersChange), 1) . '%' : '↓ ' . number_format(abs($customersChange), 1) . '%')
                ->descriptionIcon($customersChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($customersChange >= 0 ? 'success' : 'danger')
                ->chart($customersSparkline)
                ->extraAttributes([
                    'class' => 'glassmorphism-widget',
                ]),
            Stat::make('Open Tickets', number_format($openTickets))
                ->description($ticketsChange >= 0 ? '↑ ' . number_format(abs($ticketsChange), 1) . '%' : '↓ ' . number_format(abs($ticketsChange), 1) . '%')
                ->descriptionIcon($ticketsChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($ticketsChange >= 0 ? 'warning' : 'success')
                ->chart($ticketsSparkline)
                ->extraAttributes([
                    'class' => 'glassmorphism-widget',
                ]),
        ];
    }
}
