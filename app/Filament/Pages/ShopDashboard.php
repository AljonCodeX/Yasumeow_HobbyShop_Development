<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\LatestReservations;
use App\Filament\Widgets\ShopStats;
use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class ShopDashboard extends BaseDashboard
{
    protected static string $routePath = 'shop-dashboard';

    protected static ?string $title = 'Shop Dashboard';

    protected static ?string $navigationLabel = 'Shop Dashboard';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static ?int $navigationSort = -1;

    public function getWidgets(): array
    {
        return [ShopStats::class, LatestReservations::class];
    }
}