<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\Reservation;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class LatestReservations extends TableWidget
{
    use InteractsWithPageFilters;

    protected static bool $isDiscovered = false;

    protected static ?int $sort = 5;

    protected static ?string $heading = 'Latest reservations';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => Reservation::query()->dashboardFilters($this->pageFilters)->with('items.product')->latest('reserved_at')->limit(5))
            ->columns([
                TextColumn::make('id')->label('#'),
                TextColumn::make('contact_name')->label('Customer'),
                TextColumn::make('items_list')
                    ->label('Items')
                    ->state(fn (Reservation $r) => $r->items
                        ->map(fn ($i) => ($i->product?->name ?? 'Product') . ' × ' . $i->quantity)
                        ->implode(', '))
                    ->wrap(),
                TextColumn::make('amount_total')->label('Total')->money('PHP'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending', 'picked_up' => 'success',
                        'ready'                => 'info',
                        'awaiting_payment'     => 'warning',
                        'cancelled', 'expired' => 'danger',
                        default                => 'gray',
                    }),
                TextColumn::make('reserved_at')->label('Reserved')->dateTime('M d, g:i A'),
            ])
            ->recordUrl(fn (Reservation $r) => ReservationResource::getUrl('edit', ['record' => $r]))
            ->paginated(false);
    }
}