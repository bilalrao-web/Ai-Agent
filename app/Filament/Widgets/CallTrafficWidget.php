<?php

namespace App\Filament\Widgets;

use App\Models\CallLog;
use App\Filament\Traits\ChartOptionsTrait;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class CallTrafficWidget extends ChartWidget
{
    use ChartOptionsTrait;

    protected static ?string $heading = 'Call Traffic Overview';
    
    protected static ?int $sort = 6;
    
    protected int | string | array $columnSpan = 12;

    protected function getData(): array
    {
        $labels = [];
        $trafficData = [];
        
        for ($i = 23; $i >= 0; $i--) {
            $hour = Carbon::now()->subHours($i);
            $labels[] = $hour->format('H:i');
            
            $hourStart = $hour->copy()->startOfHour();
            $hourEnd = $hour->copy()->endOfHour();
            
            $trafficData[] = CallLog::whereBetween('created_at', [$hourStart, $hourEnd])->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Calls per Hour',
                    'data' => $trafficData,
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
        $mutedColor = $this->getChartMutedColor();
        $gridColor = $this->getChartGridColor();

        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'tooltip' => $this->getChartTooltipOptions(),
            ],
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'color' => $mutedColor,
                        'font' => [
                            'family' => "'DM Sans', sans-serif",
                            'size' => 11,
                        ],
                    ],
                    'border' => [
                        'display' => false,
                    ],
                ],
                'y' => [
                    'grid' => [
                        'color' => $gridColor,
                        'drawBorder' => false,
                    ],
                    'ticks' => [
                        'color' => $mutedColor,
                        'font' => [
                            'family' => "'DM Sans', sans-serif",
                            'size' => 11,
                        ],
                    ],
                    'border' => [
                        'display' => false,
                    ],
                ],
            ],
            'elements' => [
                'line' => [
                    'borderJoinStyle' => 'round',
                    'borderCapStyle' => 'round',
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
