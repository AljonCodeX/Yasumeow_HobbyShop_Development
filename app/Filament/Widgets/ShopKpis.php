<?php

namespace App\Filament\Widgets;

use App\Models\Reservation;
use App\Models\ReservationItem;
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

        return [
            Stat::make('Repeat customer rate', ($customers ? round($repeat / $customers * 100, 1) : 0) . '%')
                ->description("$repeat of $customers customers")
                ->color('success'),
            Stat::make('Avg items / reservation', $valid ? round($items / $valid, 1) : 0)
                ->description("$items items, $valid reservations")
                ->color('info'),
            Stat::make('Cancellation rate', ($total ? round($lost / $total * 100, 1) : 0) . '%')
                ->description("$lost cancelled or expired")
                ->color('warning'),
            Stat::make('Revenue / customer', '₱' . number_format($buyers ? $revenue / $buyers : 0, 2))
                ->description('₱' . number_format($revenue, 2) . ' total picked up')
                ->color('success'),
        ];
    }
}