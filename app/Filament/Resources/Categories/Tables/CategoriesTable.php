<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\Game;
use App\Models\Product;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
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
                        $ids = $records->modelKeys();

                        if (Product::whereIn('category_id', $ids)->exists() || Game::whereIn('category_id', $ids)->exists()) {
                            Notification::make()
                                ->title("Can't delete")
                                ->body('One or more selected categories still have products or games.')
                                ->danger()
                                ->send();

                            $action->cancel();
                        }
                    }),
                ]),
            ]);
    }
}
