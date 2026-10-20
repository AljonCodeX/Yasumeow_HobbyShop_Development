<?php

namespace App\Filament\Resources\Results\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ResultsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event.title')->label('Event')->searchable()->sortable(),
                TextColumn::make('placement')->label('Place')->prefix('#')->sortable(),
                TextColumn::make('user.name')->label('Player')->searchable(),
                TextColumn::make('points')->numeric()->sortable(),
            ])
            ->defaultSort('event_id', 'desc')
            ->filters([
                SelectFilter::make('event_id')->label('Event')->relationship('event', 'title'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}