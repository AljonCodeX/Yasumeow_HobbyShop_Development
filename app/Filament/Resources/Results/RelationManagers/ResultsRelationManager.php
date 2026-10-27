<?php

namespace App\Filament\Resources\Results\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class ResultsRelationManager extends RelationManager
{
    protected static string $relationship = 'results';

    protected static ?string $title = 'Placements';

    public function form(Schema $schema): Schema
    {
        $eventId = $this->getOwnerRecord()->id;
        $sameEvent = fn (Unique $rule) => $rule->where('event_id', $eventId);

        return $schema->components([
            Select::make('user_id')
                ->label('Player')
                ->options(fn () => $this->getOwnerRecord()
                    ->registrations()
                    ->where('status', 'registered')
                    ->with('user')
                    ->get()
                    ->pluck('user.name', 'user_id'))
                ->searchable()
                ->required()
                ->unique(ignoreRecord: true, modifyRuleUsing: $sameEvent),
            TextInput::make('placement')
                ->numeric()
                ->minValue(1)
                ->required()
                ->unique(ignoreRecord: true, modifyRuleUsing: $sameEvent),
            TextInput::make('points')
                ->numeric()
                ->minValue(0)
                ->default(0)
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('placement')->label('Place')->prefix('#')->sortable(),
                TextColumn::make('user.name')->label('Player'),
                TextColumn::make('points')->sortable(),
            ])
            ->defaultSort('placement')
            ->headerActions([CreateAction::make()->label('Add placement')])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}