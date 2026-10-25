<?php

namespace App\Filament\Resources\Games\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Models\Event;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

class GamesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->disk('public'),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('category.name')->label('Category')->sortable(),
                IconColumn::make('is_active')->label('Shown')->boolean(),
            ])
            ->defaultSort('name')
            ->filters([
                SelectFilter::make('category_id')->label('Category')->relationship('category', 'name'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                DeleteBulkAction::make()
                    ->before(function (DeleteBulkAction $action, Collection $records) {
                        if (Event::whereIn('game_id', $records->modelKeys())->exists()) {
                            Notification::make()
                                ->title("Can't delete")
                                ->body('One or more selected games have events.')
                                ->danger()
                                ->send();

                            $action->cancel();
                        }
                    }),
                ]),
            ]);
    }
}