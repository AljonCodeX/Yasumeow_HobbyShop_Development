<?php

namespace App\Filament\Widgets;

use App\Models\Registration;
use Filament\Widgets\ChartWidget;

class RegistrationsPerMonth extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected static ?int $sort = 2;

    protected ?string $heading = 'Sign-ups per month';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $counts = Registration::where('status', 'registered')
            ->where('registered_at', '>=', now()->startOfMonth()->subMonths(11))
            ->pluck('registered_at')
            ->countBy(fn ($d) => $d->format('Y-m'));

        $months = collect(range(11, 0))->map(fn ($i) => now()->startOfMonth()->subMonths($i));

        return [
            'labels' => $months->map(fn ($m) => $m->format('M Y'))->all(),
            'datasets' => [[
                'label' => 'Registered players',
                'data' => $months->map(fn ($m) => $counts[$m->format('Y-m')] ?? 0)->all(),
                'backgroundColor' => '#f59e0b',
            ]],
        ];
    }
}