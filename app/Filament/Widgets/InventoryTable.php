<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

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
                ->withSold()
                ->withSum(['reservationItems as on_hold' => fn ($q) => $q->whereHas(
                    'reservation',
                    fn ($r) => $r->whereIn('status', ['awaiting_payment', 'pending', 'ready'])
                )], 'quantity'))
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('category.name')->label('Category')->sortable(),
                TextColumn::make('price')->money('PHP')->sortable(),
                TextColumn::make('stock_qty')
                    ->label('Available')
                    ->numeric()
                    ->sortable()
                    ->tooltip('On the shelf, can still be reserved'),
                TextColumn::make('on_hold')
                    ->label('On hold')
                    ->state(fn (Product $p) => (int) $p->on_hold)
                    ->tooltip('Reserved by customers, not picked up yet'),
                TextColumn::make('total_in_shop')
                    ->label('Total in shop')
                    ->state(fn (Product $p) => $p->stock_qty + (int) $p->on_hold)
                    ->tooltip('Available + on hold'),
                TextColumn::make('sold_count')
                    ->label('Sold')
                    ->state(fn (Product $p) => (int) $p->sold_count)
                    ->sortable()
                    ->tooltip('Units picked up so far'),
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
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function ($livewire) {
                        // Same rows, filters and sorting you see on screen
                        $products = $livewire->getFilteredSortedTableQuery()->get();

                        $path = tempnam(sys_get_temp_dir(), 'inventory');

                        $writer = new \OpenSpout\Writer\XLSX\Writer();
                        $writer->openToFile($path);

                        $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues([
                            'Name', 'Category', 'Price', 'Available', 'On hold', 'Total in shop', 'Sold', 'Value', 'Status',
                        ]));

                        foreach ($products as $p) {
                            $writer->addRow(\OpenSpout\Common\Entity\Row::fromValues([
                                $p->name,
                                $p->category?->name ?? '',
                                (float) $p->price,
                                (int) $p->stock_qty,
                                (int) $p->on_hold,
                                (int) $p->stock_qty + (int) $p->on_hold,
                                (int) $p->sold_count,
                                (float) $p->price * $p->stock_qty,
                                match ($p->stockStatus()) {
                                    'out' => 'Sold out',
                                    'low' => 'Low stock',
                                    default => 'In stock',
                                },
                            ]));
                        }

                        $writer->close();

                        return response()->streamDownload(function () use ($path) {
                            readfile($path);
                            @unlink($path);
                        }, 'inventory-' . now()->format('Y-m-d') . '.xlsx', [
                            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ]);
                    }),
            ])
            ->recordActions([
                Action::make('restock')
                    ->label('Restock')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->modalHeading(fn (Product $record) => 'Restock ' . $record->name)
                    ->schema([
                        TextInput::make('quantity')
                            ->label('Units received')
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->required(),
                    ])
                    ->action(function (Product $record, array $data) {
                        $record->increment('stock_qty', (int) $data['quantity']);

                        // The website search list keeps stock status for 5 minutes, so refresh it now
                        Cache::forget('search-index');

                        Notification::make()
                            ->title($record->name . ' restocked')
                            ->body('Added ' . (int) $data['quantity'] . ' units. Available now: ' . $record->fresh()->stock_qty)
                            ->success()
                            ->send();
                    }),
            ]);
    }
}