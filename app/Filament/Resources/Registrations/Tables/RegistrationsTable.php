<?php

namespace App\Filament\Resources\Registrations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

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
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}