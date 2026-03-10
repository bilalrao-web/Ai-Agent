<?php

namespace App\Filament\Widgets;

use App\Models\CallLog;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class CallTrafficWidget extends ChartWidget
{
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
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'tooltip' => [
                    'backgroundColor' => 'rgba(17, 24, 39, 0.9)',
                    'titleColor' => 'rgba(255, 255, 255, 0.9)',
                    'bodyColor' => 'rgba(255, 255, 255, 0.7)',
                    'borderColor' => 'rgba(255, 255, 255, 0.1)',
                    'borderWidth' => 1,
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'color' => 'rgba(255, 255, 255, 0.5)',
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
                        'color' => 'rgba(255, 255, 255, 0.03)',
                        'drawBorder' => false,
                    ],
                    'ticks' => [
                        'color' => 'rgba(255, 255, 255, 0.5)',
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
