<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class InventoryStats extends StatsOverviewWidget
{
    // Only shown on the Inventory report, not on the dashboard
    protected static bool $isDiscovered = false;

    protected function getStats(): array
    {
        return [
            Stat::make('Products', Product::count()),
            Stat::make('Units in stock', number_format((int) Product::sum('stock_qty'))),
            Stat::make('Stock value', '₱' . number_format((float) Product::sum(DB::raw('price * stock_qty')), 2)),
            Stat::make('Low stock (5 or fewer)', Product::where('stock_qty', '>', 0)->where('stock_qty', '<=', 5)->count())
                ->color('warning'),
            Stat::make('Sold out', Product::where('stock_qty', '<=', 0)->count())
                ->color('danger'),
        ];
    }
}