<?php

namespace App\Filament\Resources\Results\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class ResultForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('event_id')->label('Event')->relationship('event', 'title')->searchable()->preload()->required(),
            Select::make('user_id')->label('Player')->relationship('user', 'name')->searchable()->preload()->required()
                ->unique(
                    ignoreRecord: true,
                    modifyRuleUsing: fn (Unique $rule, Get $get) => $rule->where('event_id', $get('event_id')),
                )
                ->validationMessages(['unique' => 'This player already has a result for this event.']),
            TextInput::make('placement')->numeric()->integer()->minValue(1)->required(),
            TextInput::make('points')->numeric()->integer()->minValue(0)->default(0)->required(),
        ]);
    }
}