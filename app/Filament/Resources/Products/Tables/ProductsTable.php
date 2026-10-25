<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\ReservationItem;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable(),
                TextColumn::make('price')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('stock_qty')
                    ->numeric()
                    ->sortable(),
                ImageColumn::make('image')->disk('public'),
                IconColumn::make('is_featured')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                DeleteBulkAction::make()
                    ->before(function (DeleteBulkAction $action, Collection $records) {
                        if (ReservationItem::whereIn('product_id', $records->modelKeys())->exists()) {
                            Notification::make()
                                ->title("Can't delete")
                                ->body('One or more selected products are part of existing reservations.')
                                ->danger()
                                ->send();

                            $action->cancel();
                        }
                    }),
                ]),
            ]);
    }
}
