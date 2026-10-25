<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\Reservation;
use App\Models\ReservationItem;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class InventoryStats extends StatsOverviewWidget
{
    // Only shown on the Inventory report, not on the dashboard
    protected static bool $isDiscovered = false;

    protected int|array|null $columns = 4;

    protected function getStats(): array
    {
        $open = ['awaiting_payment', 'pending', 'ready'];

        // Stock on the shelf
        $stockValue = (float) Product::sum(DB::raw('price * stock_qty'));
        $units = (int) Product::sum('stock_qty');
        $onHold = (int) ReservationItem::whereHas('reservation', fn ($q) => $q->whereIn('status', $open))->sum('quantity');

        // Units picked up per month (last 12 months), used for "Sold this month" and its chart
        $months = collect(range(11, 0))->map(fn ($i) => now()->startOfMonth()->subMonths($i));

        $byMonth = Reservation::where('status', 'picked_up')
            ->where('reserved_at', '>=', $months->first())
            ->with('items')
            ->get()
            ->groupBy(fn ($r) => $r->reserved_at->format('Y-m'))
            ->map(fn ($rows) => (int) $rows->sum(fn ($r) => $r->items->sum('quantity')));

        $soldThisMonth = $byMonth[now()->format('Y-m')] ?? 0;
        $trend = $months->map(fn ($m) => $byMonth[$m->format('Y-m')] ?? 0)->all();

        // Best seller of all time
        $best = Product::withSold()->orderByDesc('sold_count')->orderBy('name')->first();
        $bestSold = (int) ($best?->sold_count ?? 0);

        // Slow movers: in stock, older than 30 days, no pickup in the last 30 days
        $slow = Product::where('stock_qty', '>', 0)
            ->where('created_at', '<=', now()->subDays(30))
            ->whereDoesntHave('reservationItems', fn ($q) => $q->whereHas(
                'reservation',
                fn ($r) => $r->where('status', 'picked_up')->where('reserved_at', '>=', now()->subDays(30))
            ))
            ->get(['id', 'price', 'stock_qty']);
        $slowValue = (float) $slow->sum(fn ($p) => (float) $p->price * $p->stock_qty);

        return [
            Stat::make('Retail stock value', '₱' . number_format($stockValue, 2))
                ->description('Available units at selling price')
                ->descriptionIcon(Heroicon::OutlinedBanknotes, IconPosition::After)
                ->color('success'),
            Stat::make('Products', Product::count())
                ->description('In the shop')
                ->descriptionIcon(Heroicon::OutlinedSquares2x2, IconPosition::After)
                ->color('info'),
            Stat::make('Units available', number_format($units))
                ->description($onHold > 0 ? number_format($onHold) . ' on hold' : 'None on hold')
                ->descriptionIcon(Heroicon::OutlinedArchiveBox, IconPosition::After)
                ->color('info'),
            Stat::make('Low stock (5 or fewer)', Product::where('stock_qty', '>', 0)->where('stock_qty', '<=', 5)->count())
                ->description('Needs restocking soon')
                ->descriptionIcon(Heroicon::OutlinedExclamationTriangle, IconPosition::After)
                ->color('warning'),
            Stat::make('Sold out', Product::where('stock_qty', '<=', 0)->count())
                ->description('Products at 0')
                ->descriptionIcon(Heroicon::OutlinedXCircle, IconPosition::After)
                ->color('danger'),
            Stat::make('Sold this month', number_format($soldThisMonth))
                ->description('Units picked up')
                ->descriptionIcon(Heroicon::OutlinedShoppingCart, IconPosition::After)
                ->chart($trend)
                ->color('info'),
            Stat::make('Best seller', $bestSold > 0 ? \Illuminate\Support\Str::limit($best->name, 18) : '—')
                ->extraAttributes($bestSold > 0 ? ['title' => $best->name] : [])
                ->description($bestSold > 0 ? number_format($bestSold) . ' units picked up' : 'No pickups yet')
                ->descriptionIcon(Heroicon::OutlinedTrophy, IconPosition::After)
                ->color('success'),
            Stat::make('Slow movers', $slow->count())
                ->description($slow->count() > 0 ? '₱' . number_format($slowValue, 2) . ' tied up, no pickups in 30 days' : 'Nothing slow right now')
                ->descriptionIcon(Heroicon::OutlinedClock, IconPosition::After)
                ->color('gray'),
        ];
    }
}