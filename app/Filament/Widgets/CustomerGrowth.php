<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class CustomerGrowth extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected static ?int $sort = 4;

    protected ?string $heading = 'Customer Growth';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $counts = User::where('created_at', '>=', now()->startOfMonth()->subMonths(11))
            ->pluck('created_at')
            ->countBy(fn ($d) => $d->format('Y-m'));

        $months = collect(range(11, 0))->map(fn ($i) => now()->startOfMonth()->subMonths($i));

        return [
            'labels' => $months->map(fn ($m) => $m->format('M Y'))->all(),
            'datasets' => [[
                'label' => 'New customers',
                'data' => $months->map(fn ($m) => $counts[$m->format('Y-m')] ?? 0)->all(),
                'borderColor' => '#22c55e',
                'backgroundColor' => 'rgba(34,197,94,.15)',
                'fill' => true,
                'tension' => 0.3,
            ]],
        ];
    }
}