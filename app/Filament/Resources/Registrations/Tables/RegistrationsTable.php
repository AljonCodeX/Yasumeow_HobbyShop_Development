<?php

namespace App\Filament\Resources\Registrations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions\Action;

class RegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event.title')->label('Event')->searchable()->sortable(),
                TextColumn::make('user.name')->label('Player')->searchable()->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'registered' ? 'success' : 'danger'),
                TextColumn::make('payment_method')->label('Pay via')->placeholder('Free'),
                TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'unpaid' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('registered_at')->dateTime('M d, g:i A')->sortable(),
            ])
            ->defaultSort('registered_at', 'desc')
            ->filters([
                SelectFilter::make('event_id')->label('Event')->relationship('event', 'title'),
                SelectFilter::make('status')->options([
                    'registered' => 'Registered',
                    'cancelled'  => 'Cancelled',
                ]),
            ])
            ->recordActions([
                Action::make('markPaid')
                    ->label('Mark paid')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn ($record) => $record->payment_status === 'unpaid')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['payment_status' => 'paid'])),
                EditAction::make(),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}