<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AIAnalyticsWidget;
use App\Filament\Widgets\BusinessMetricsWidget;
use App\Filament\Widgets\CallSourceWidget;
use App\Filament\Widgets\OrderStatusWidget;
use App\Filament\Widgets\TicketStatusWidget;
use App\Filament\Widgets\RevenueChartWidget;
use App\Filament\Widgets\RecentCallsWidget;
use App\Filament\Widgets\LiveCallStatsWidget;
use App\Filament\Widgets\TopCalledNumbersWidget;
use App\Filament\Widgets\CallOutcomeSummaryWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    public function getColumns(): int|string|array
    {
        return 12;
    }

    public function getWidgets(): array
    {
        return [
            BusinessMetricsWidget::class,
            AIAnalyticsWidget::class,
            CallSourceWidget::class,
            RevenueChartWidget::class,
            TicketStatusWidget::class,
            RecentCallsWidget::class,
            CallOutcomeSummaryWidget::class,
            TopCalledNumbersWidget::class,
            OrderStatusWidget::class,
        ];
    }
}
