<?php

namespace App\Filament\Resources\Results\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ResultsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Event')->searchable()->sortable(),
                TextColumn::make('game.name')->label('Game'),
                TextColumn::make('event_date')->label('Date')->dateTime('M j, Y g:i A')->sortable(),
                TextColumn::make('players_count')->label('Players'),
                TextColumn::make('results_count')
                    ->label('Results entered')
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'warning'),
            ])
            ->defaultSort('event_date', 'desc')
            ->filters([
                Filter::make('needs_results')
                    ->label('Needs results')
                    ->query(fn (Builder $query) => $query->doesntHave('results')),
            ])
            ->recordActions([
                EditAction::make()->label('Enter results'),
            ]);
    }
}