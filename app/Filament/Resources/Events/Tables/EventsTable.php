<?php

namespace App\Filament\Resources\Events\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use App\Models\Event;
use App\Models\Registration;
use App\Models\Result;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount([
    'registrations as taken_count' => fn ($r) => $r->where('status', 'registered'),
    ]))
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('game.name')
                    ->label('Game')
                    ->sortable(),
                TextColumn::make('event_date')
                    ->label('Date and time')
                    ->dateTime('M d, Y g:i A')
                    ->sortable(),
                TextColumn::make('slots')
                    ->label('Slots')
                    ->state(fn (Event $e) => $e->taken_count . ' / ' . $e->max_slots),
                TextColumn::make('price')
                    ->label('Fee')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open'      => 'success',
                        'closed'    => 'warning',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    }),
            ])
            ->defaultSort('event_date', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'open'      => 'Open',
                        'closed'    => 'Closed',
                        'cancelled' => 'Cancelled',
                        'completed' => 'Completed',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function (DeleteBulkAction $action, Collection $records) {
                            $ids = $records->modelKeys();

                            $hasPlayers = Event::whereIn('id', $ids)
                                ->where('status', '!=', 'cancelled')
                                ->whereHas('registrations', fn ($q) => $q->where('status', 'registered'))
                                ->exists();

                            if ($hasPlayers || Result::whereIn('event_id', $ids)->exists()) {
                                Notification::make()
                                    ->title("Can't delete")
                                    ->body('One or more selected events have registered players or results. Set them to Cancelled first.')
                                    ->danger()
                                    ->send();

                                $action->cancel();
                            }
                        }),
                ]),
            ]);
    }
}