<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class InventoryTable extends TableWidget
{
    protected static bool $isDiscovered = false;

    protected static ?string $heading = 'Stock levels';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => Product::query()
                ->with('category')
                ->withSum(['reservationItems as on_hold' => fn ($q) => $q->whereHas(
                    'reservation',
                    fn ($r) => $r->whereIn('status', ['awaiting_payment', 'pending', 'ready'])
                )], 'quantity'))
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('category.name')->label('Category')->sortable(),
                TextColumn::make('price')->money('PHP')->sortable(),
                TextColumn::make('stock_qty')->label('In stock')->numeric()->sortable(),
                TextColumn::make('on_hold')
                    ->label('On hold')
                    ->state(fn (Product $p) => (int) $p->on_hold)
                    ->tooltip('Reserved by customers, not picked up yet'),
                TextColumn::make('stock_value')
                    ->label('Value')
                    ->state(fn (Product $p) => (float) $p->price * $p->stock_qty)
                    ->money('PHP'),
                TextColumn::make('status')
                    ->badge()
                    ->state(fn (Product $p) => match ($p->stockStatus()) {
                        'out' => 'Sold out',
                        'low' => 'Low stock',
                        default => 'In stock',
                    })
                    ->color(fn (string $state) => match ($state) {
                        'Sold out' => 'danger',
                        'Low stock' => 'warning',
                        default => 'success',
                    }),
            ])
            ->defaultSort('stock_qty', 'asc')
            ->filters([
                SelectFilter::make('level')
                    ->label('Stock level')
                    ->options(['low' => 'Low stock', 'out' => 'Sold out'])
                    ->query(fn (Builder $query, array $data) => match ($data['value'] ?? null) {
                        'low' => $query->where('stock_qty', '>', 0)->where('stock_qty', '<=', 5),
                        'out' => $query->where('stock_qty', '<=', 0),
                        default => $query,
                    }),
                SelectFilter::make('category_id')->label('Category')->relationship('category', 'name'),
            ]);
    }
}