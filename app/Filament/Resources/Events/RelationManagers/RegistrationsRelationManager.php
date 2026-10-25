<?php

namespace App\Filament\Resources\Events\RelationManagers;

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
            ]);
    }
}