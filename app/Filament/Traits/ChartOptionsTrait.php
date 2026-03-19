<?php

namespace App\Filament\Traits;

trait ChartOptionsTrait
{
    protected function isDarkMode(): bool
    {
        $candidates = [
            request()->cookie('filament_theme'),
            request()->cookie('theme'),
            request()->cookie('theme_mode'),
            request()->cookie('filament_theme_mode'),
            session('filament.theme'),
            session('theme'),
            session('theme_mode'),
        ];

        foreach ($candidates as $value) {
            if (is_string($value) && strtolower($value) === 'dark') {
                return true;
            }
        }

        return false;
    }

    protected function getChartTextColor(): string
    {
        return $this->isDarkMode()
            ? 'rgba(255, 255, 255, 0.8)'
            : 'rgba(55, 65, 81, 0.9)';
    }

    protected function getChartMutedColor(): string
    {
        return $this->isDarkMode()
            ? 'rgba(255, 255, 255, 0.5)'
            : 'rgba(55, 65, 81, 0.6)';
    }

    protected function getChartGridColor(): string
    {
        return $this->isDarkMode()
            ? 'rgba(255, 255, 255, 0.06)'
            : 'rgba(0, 0, 0, 0.08)';
    }

    protected function getChartTooltipOptions(): array
    {
        return $this->isDarkMode()
            ? [
                'backgroundColor' => 'rgba(17, 24, 39, 0.95)',
                'titleColor' => 'rgba(255, 255, 255, 0.9)',
                'bodyColor' => 'rgba(255, 255, 255, 0.7)',
                'borderColor' => 'rgba(255, 255, 255, 0.1)',
                'borderWidth' => 1,
                'padding' => 10,
            ]
            : [
                'backgroundColor' => 'rgba(255, 255, 255, 0.95)',
                'titleColor' => 'rgba(17, 24, 39, 0.9)',
                'bodyColor' => 'rgba(55, 65, 81, 0.8)',
                'borderColor' => 'rgba(0, 0, 0, 0.1)',
                'borderWidth' => 1,
                'padding' => 10,
            ];
    }
}

