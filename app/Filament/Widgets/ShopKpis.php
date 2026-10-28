<?php

namespace App\Filament\Widgets;

use App\Models\Rating;
use App\Models\Reservation;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ShopKpis extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static bool $isDiscovered = false;

    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $dead = ['cancelled', 'expired'];
        $open = ['awaiting_payment', 'pending', 'ready']; // to count only 'pending', change this to ['pending']
        $base = fn () => Reservation::dashboardFilters($this->pageFilters);

        $revenue = (float) Reservation::dashboardFilters($this->pageFilters, 'picked_up_at')
            ->where('status', 'picked_up')
            ->sum('amount_total');
        $total = $base()->whereNotIn('status', $dead)->count();
        $pending = $base()->whereIn('status', $open)->count();

        $ratings = Rating::whereHas('reservation', fn ($q) => $q->dashboardFilters($this->pageFilters));
        $rated = $ratings->count();
        $satisfaction = $rated ? round((float) $ratings->avg('stars') / 5 * 100) : null;

        // Trend lines: last 12 months, same status/category filters, date filter ignored
        $months = collect(range(11, 0))->map(fn ($i) => now()->startOfMonth()->subMonths($i));
        $byMonth = Reservation::dashboardFilters(array_merge($this->pageFilters ?? [], ['startDate' => null, 'endDate' => null]))
            ->where('reserved_at', '>=', $months->first())
            ->with('rating')
            ->get()
            ->groupBy(fn ($r) => $r->reserved_at->format('Y-m'));
        $trend = fn (callable $f) => $months
            ->map(fn ($m) => $f($byMonth->get($m->format('Y-m'), collect())))
            ->all();
        $revenueByMonth = Reservation::dashboardFilters(array_merge($this->pageFilters ?? [], ['startDate' => null, 'endDate' => null]))
            ->where('status', 'picked_up')
            ->where('picked_up_at', '>=', $months->first())
            ->get(['picked_up_at', 'amount_total'])
            ->groupBy(fn ($r) => $r->picked_up_at->format('Y-m'))
            ->map(fn ($rows) => (float) $rows->sum('amount_total'));

        return [
            Stat::make('Total Revenue', '₱' . number_format($revenue, 2))
                ->description('From picked up reservations')
                ->descriptionIcon(Heroicon::OutlinedBanknotes, IconPosition::After)
                ->chart($months->map(fn ($m) => $revenueByMonth[$m->format('Y-m')] ?? 0)->all())
                ->color('success'),
            Stat::make('Total Reservations', $total)
                ->description('Excluding cancelled or expired')
                ->descriptionIcon(Heroicon::OutlinedShoppingCart, IconPosition::After)
                ->chart($trend(fn ($rows) => $rows->reject(fn ($r) => in_array($r->status, $dead))->count()))
                ->color('info'),
            Stat::make('Customer Satisfaction', $satisfaction === null ? '—' : $satisfaction . '%')
                ->description($rated ? "$rated rating" . ($rated > 1 ? 's' : '') : 'No ratings yet')
                ->descriptionIcon(Heroicon::OutlinedStar, IconPosition::After)
                ->chart($trend(fn ($rows) => round((float) $rows->pluck('rating.stars')->filter()->avg() / 5 * 100)))
                ->color('warning'),
            Stat::make('Pending Reservations', $pending)
                ->description('Waiting for payment or pickup')
                ->descriptionIcon(Heroicon::OutlinedClock, IconPosition::After)
                ->chart($trend(fn ($rows) => $rows->whereIn('status', $open)->count()))
                ->color('danger'),
        ];
    }
}