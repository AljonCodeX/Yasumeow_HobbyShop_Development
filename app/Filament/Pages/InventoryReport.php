<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\InventoryStats;
use App\Filament\Widgets\InventoryTable;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class InventoryReport extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static string|UnitEnum|null $navigationGroup = 'Reports';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Inventory';

    public function getTitle(): string
    {
        return 'Inventory report';
    }

    protected function getHeaderWidgets(): array
    {
        return [InventoryStats::class];
    }

    protected function getFooterWidgets(): array
    {
        return [InventoryTable::class];
    }
}