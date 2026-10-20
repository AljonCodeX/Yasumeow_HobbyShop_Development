<?php

namespace App\Filament\Widgets;

use App\Models\Reservation;
use Filament\Widgets\ChartWidget;

class ReservationsYoY extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected static ?int $sort = 3;

    protected ?string $heading = 'Reservation Year-over-Year';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        // shortcut: grouped in PHP, fine for a small shop; move to SQL if reservations reach tens of thousands
        $counts = Reservation::where('reserved_at', '>=', now()->startOfMonth()->subMonths(23))
            ->pluck('reserved_at')
            ->countBy(fn ($d) => $d->format('Y-m'));

        $months = collect(range(11, 0))->map(fn ($i) => now()->startOfMonth()->subMonths($i));

        return [
            'labels' => $months->map(fn ($m) => $m->format('M Y'))->all(),
            'datasets' => [
                [
                    'label' => 'Last 12 months',
                    'data' => $months->map(fn ($m) => $counts[$m->format('Y-m')] ?? 0)->all(),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59,130,246,.15)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Prior 12 months',
                    'data' => $months->map(fn ($m) => $counts[$m->copy()->subYear()->format('Y-m')] ?? 0)->all(),
                    'borderColor' => '#9ca3af',
                    'borderDash' => [5, 5],
                    'tension' => 0.3,
                ],
            ],
        ];
    }
}