<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\MonthlyReservations;
use App\Filament\Widgets\MonthlyRevenue;
use App\Filament\Widgets\LatestReservations;
use App\Filament\Widgets\ShopKpis;
use App\Models\Category;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ShopDashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static string $routePath = 'shop-dashboard';

    protected static ?string $title = 'Shop Dashboard';

    protected static ?string $navigationLabel = 'Shop Dashboard';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static ?int $navigationSort = -1;

    public function filtersForm(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make()
                ->columnSpanFull()
                ->extraAttributes(['class' => 'w-full'])
                ->schema([
            DatePicker::make('startDate')
                ->label('Start date')
                ->native(false)
                ->displayFormat('d/m/Y')
                ->placeholder('dd/mm/yyyy')
                ->suffixIcon(Heroicon::OutlinedCalendarDays)
                ->maxDate(fn ($get) => $get('endDate')),
            DatePicker::make('endDate')
                ->label('End date')
                ->native(false)
                ->displayFormat('d/m/Y')
                ->placeholder('dd/mm/yyyy')
                ->suffixIcon(Heroicon::OutlinedCalendarDays)
                ->minDate(fn ($get) => $get('startDate')),
                    Select::make('status')
                        ->label('Status')
                        ->placeholder('All statuses')
                        ->options([
                            'pending'          => 'Reserved',
                            'awaiting_payment' => 'Awaiting payment',
                            'ready'            => 'Ready for pickup',
                            'picked_up'        => 'Picked up',
                            'cancelled'        => 'Cancelled',
                        ]),
                    Select::make('category')
                        ->label('Product category')
                        ->placeholder('All categories')
                        ->options(fn () => Category::orderBy('name')->pluck('name', 'id')),
                ])
                ->columns(['default' => 1, 'md' => 4]),
        ]);
    }

    public function getWidgets(): array
    {
        return [ShopKpis::class, MonthlyRevenue::class, MonthlyReservations::class, LatestReservations::class];
    }
}