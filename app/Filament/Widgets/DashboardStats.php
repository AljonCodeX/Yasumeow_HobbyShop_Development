<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\Product;
use App\Models\Reservation;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $sales = Reservation::where('status', 'picked_up')
            ->whereBetween('updated_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('amount_total');

        return [
            Stat::make('Reserved (to prepare)', Reservation::where('status', 'pending')->count())
                ->description('Waiting for pickup')
                ->color('info'),
            Stat::make('Ready for pickup', Reservation::where('status', 'ready')->count())
                ->color('success'),
            Stat::make('Awaiting payment', Reservation::where('status', 'awaiting_payment')->count())
                ->description('QR Ph, released after 30 min')
                ->color('warning'),
            Stat::make('Sales this month', '₱' . number_format((float) $sales, 2))
                ->description('Picked-up reservations'),
            Stat::make('Low or sold out', Product::where('stock_qty', '<=', 5)->count())
                ->description('5 or fewer in stock')
                ->color('danger'),
            Stat::make('Upcoming events', Event::upcoming()->count()),
        ];
    }
}