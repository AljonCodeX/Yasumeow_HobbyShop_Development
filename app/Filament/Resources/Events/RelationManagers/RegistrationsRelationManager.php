<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RegistrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'registrations';

    protected static ?string $title = 'Players';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Player')->searchable(),
                TextColumn::make('user.email')->label('Email')->searchable(),
                TextColumn::make('user.phone')->label('Phone'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'registered' ? 'success' : 'danger'),
                TextColumn::make('registered_at')->label('Registered')->dateTime('M d, g:i A')->sortable(),
            ])
            ->defaultSort('registered_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options([
                    'registered' => 'Registered',
                    'cancelled'  => 'Cancelled',
                ]),
            ])
            ->headerActions([
                Action::make('addPlayer')
                    ->label('Add player')
                    ->icon('heroicon-o-user-plus')
                    ->hidden(fn () => in_array($this->getOwnerRecord()->status, ['cancelled', 'completed']))
                    ->schema([
                        Select::make('user_id')
                            ->label('Player')
                            ->options(function () {
                                $already = $this->getOwnerRecord()->registrations()
                                    ->where('status', 'registered')->pluck('user_id');

                                return User::whereNotIn('id', $already)->orderBy('name')->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $event = $this->getOwnerRecord();

                        if ($event->slotsLeft() === 0) {
                            Notification::make()->title('Event is full')->danger()->send();

                            return;
                        }

                        // Also re-activates a player who cancelled earlier
                        $event->registrations()->updateOrCreate(
                            ['user_id' => $data['user_id']],
                            ['status' => 'registered', 'registered_at' => now()]
                        );

                        Notification::make()->title('Player added')->success()->send();
                    }),
            ])
            ->recordActions([
                Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-user-minus')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'registered')
                    ->action(fn ($record) => $record->update(['status' => 'cancelled'])),
            ]);
    }
}