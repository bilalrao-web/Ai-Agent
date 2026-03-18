<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class OpenTicketsWidget extends BaseWidget
{
    protected static ?int $sort = 13;
    
    protected int | string | array $columnSpan = 'full';
    
    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
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
        
        $change = $previousWeekTickets > 0 
            ? (($lastWeekTickets - $previousWeekTickets) / $previousWeekTickets) * 100 
            : 0;
        
        
        $sparklineData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $sparklineData[] = Ticket::whereIn('status', ['open', 'in_progress'])
                ->whereDate('created_at', $date)
                ->count();
        }

        return [
            Stat::make('Open Tickets', number_format($openTickets))
                ->description($change >= 0 ? '↑ ' . number_format(abs($change), 1) . '%' : '↓ ' . number_format(abs($change), 1) . '%')
                ->descriptionIcon($change >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($change >= 0 ? 'warning' : 'success')
                ->chart($sparklineData)
                ->extraAttributes([
                    'class' => 'glassmorphism-widget',
                ]),
        ];
    }
}
