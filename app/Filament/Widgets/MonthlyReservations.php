<?php

namespace App\Filament\Widgets;

use App\Models\Reservation;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class MonthlyReservations extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static bool $isDiscovered = false;

    protected static ?int $sort = 4;

    protected ?string $heading = 'Monthly Reservations';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $months = collect(range(11, 0))->map(fn ($i) => now()->startOfMonth()->subMonths($i));

        // Same status/category filters as the cards, date range ignored (always 12 months)
        $counts = Reservation::dashboardFilters(array_merge($this->pageFilters ?? [], ['startDate' => null, 'endDate' => null]))
            ->whereNotIn('status', ['cancelled', 'expired'])
            ->where('reserved_at', '>=', $months->first())
            ->pluck('reserved_at')
            ->countBy(fn ($d) => $d->format('Y-m'));

        return [
            'labels' => $months->map(fn ($m) => $m->format('M Y'))->all(),
            'datasets' => [[
                'label' => 'Reservations',
                'data' => $months->map(fn ($m) => $counts[$m->format('Y-m')] ?? 0)->all(),
                'borderColor' => '#3b82f6',
                'backgroundColor' => 'rgba(59,130,246,.15)',
                'fill' => true,
                'tension' => 0.3,
            ]],
        ];
    }
}