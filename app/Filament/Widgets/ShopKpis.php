<?php

namespace App\Filament\Widgets;

use App\Models\Reservation;
use App\Models\ReservationItem;
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
        $base = fn () => Reservation::dashboardFilters($this->pageFilters);

        $perUser = $base()->whereNotIn('status', $dead)
            ->selectRaw('user_id, count(*) as c')->groupBy('user_id')->pluck('c');
        $customers = $perUser->count();
        $repeat = $perUser->filter(fn ($c) => $c > 1)->count();

        $total = $base()->count();
        $lost = $base()->whereIn('status', $dead)->count();

        $valid = $base()->whereNotIn('status', $dead)->count();
        $items = ReservationItem::whereHas('reservation', fn ($q) => $q->dashboardFilters($this->pageFilters)->whereNotIn('status', $dead))->sum('quantity');

        $revenue = (float) $base()->where('status', 'picked_up')->sum('amount_total');
        $buyers = $base()->where('status', 'picked_up')->distinct()->count('user_id');

        // Trend lines: last 12 months, same status/category filters, date filter ignored
        $months = collect(range(11, 0))->map(fn ($i) => now()->startOfMonth()->subMonths($i));
        $byMonth = Reservation::dashboardFilters(array_merge($this->pageFilters ?? [], ['startDate' => null, 'endDate' => null]))
            ->where('reserved_at', '>=', $months->first())
            ->with('items')
            ->get()
            ->groupBy(fn ($r) => $r->reserved_at->format('Y-m'));
        $trend = fn (callable $f) => $months
            ->map(fn ($m) => $f($byMonth->get($m->format('Y-m'), collect())))
            ->all();
        $valids = fn ($rows) => $rows->reject(fn ($r) => in_array($r->status, $dead));

        return [
            Stat::make('Repeat Customer Rate', ($customers ? round($repeat / $customers * 100, 1) : 0) . '%')
                ->description("$repeat of $customers customers")
                ->descriptionIcon(Heroicon::OutlinedArrowPath, IconPosition::After)
                ->chart($trend(function ($rows) use ($valids) {
                    $c = $valids($rows)->countBy('user_id');
                    return $c->count() ? round($c->filter(fn ($n) => $n > 1)->count() / $c->count() * 100, 1) : 0;
                }))
                ->color('success'),
            Stat::make('Avg Items / Reservation', $valid ? round($items / $valid, 1) : 0)
                ->description("$items items, $valid reservations")
                ->descriptionIcon(Heroicon::OutlinedShoppingCart, IconPosition::After)
                ->chart($trend(function ($rows) use ($valids) {
                    $v = $valids($rows);
                    return $v->count() ? round($v->sum(fn ($r) => $r->items->sum('quantity')) / $v->count(), 1) : 0;
                }))
                ->color('info'),
            Stat::make('Cancellation Rate', ($total ? round($lost / $total * 100, 1) : 0) . '%')
                ->description("$lost cancelled or expired")
                ->descriptionIcon(Heroicon::OutlinedXCircle, IconPosition::After)
                ->chart($trend(fn ($rows) => $rows->count()
                    ? round($rows->filter(fn ($r) => in_array($r->status, $dead))->count() / $rows->count() * 100, 1)
                    : 0))
                ->color('warning'),
            Stat::make('Revenue / Customer', '₱' . number_format($buyers ? $revenue / $buyers : 0, 2))
                ->description('₱' . number_format($revenue, 2) . ' total picked up')
                ->descriptionIcon(Heroicon::OutlinedBanknotes, IconPosition::After)
                ->chart($trend(function ($rows) {
                    $p = $rows->where('status', 'picked_up');
                    $u = $p->pluck('user_id')->unique()->count();
                    return $u ? round((float) $p->sum('amount_total') / $u, 2) : 0;
                }))
                ->color('success'),
        ];
    }
}