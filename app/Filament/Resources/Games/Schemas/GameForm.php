<?php

namespace App\Filament\Resources\Games\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class GameForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                Select::make('section')
                    ->options([
                        'Board Games' => 'Board Games',
                        'Physical Toy Games' => 'Physical Toy Games',
                        'PS5 Games' => 'PS5 Games',
                    ])
                    ->required(),
                TextInput::make('type')
                    ->placeholder('Card Game, Racing, Fighting...'),
                TextInput::make('icon')
                    ->placeholder('bi-dice-5-fill')
                    ->helperText('Bootstrap Icons class name'),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
                Textarea::make('description')
                    ->rows(2)
                    ->columnSpanFull(),
                Textarea::make('about')
                    ->rows(4)
                    ->columnSpanFull(),
                Repeater::make('guide')
                    ->label('How to play (steps)')
                    ->simple(
                        TextInput::make('step')->required()
                    )
                    ->reorderable()
                    ->addActionLabel('Add step')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Show on website')
                    ->default(true),
            ]);
    }
}