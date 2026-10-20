<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('phone')->searchable(),
                IconColumn::make('is_admin')->label('Admin')->boolean(),
                TextColumn::make('created_at')->label('Joined')->dateTime('M d, Y')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([TernaryFilter::make('is_admin')->label('Admin')])
            ->recordActions([EditAction::make()]);
    }
}