<?php

namespace App\Filament\Widgets;

use App\Models\Reservation;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class MonthlyRevenue extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static bool $isDiscovered = false;

    protected static ?int $sort = 3;

    protected ?string $heading = 'Monthly Revenue';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $months = collect(range(11, 0))->map(fn ($i) => now()->startOfMonth()->subMonths($i));

        // Same status/category filters as the cards, date range ignored (always 12 months)
        $totals = Reservation::dashboardFilters(array_merge($this->pageFilters ?? [], ['startDate' => null, 'endDate' => null]))
            ->where('status', 'picked_up')
            ->where('reserved_at', '>=', $months->first())
            ->get(['reserved_at', 'amount_total'])
            ->groupBy(fn ($r) => $r->reserved_at->format('Y-m'))
            ->map(fn ($rows) => (float) $rows->sum('amount_total'));

        return [
            'labels' => $months->map(fn ($m) => $m->format('M Y'))->all(),
            'datasets' => [[
                'label' => 'Revenue (₱)',
                'data' => $months->map(fn ($m) => $totals[$m->format('Y-m')] ?? 0)->all(),
                'backgroundColor' => '#22c55e',
            ]],
        ];
    }
}