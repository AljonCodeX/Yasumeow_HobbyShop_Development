<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Select::make('game_id')
                    ->label('Game')
                    ->relationship('game', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                DateTimePicker::make('event_date')
                    ->label('Date and time')
                    ->required(),
                TextInput::make('max_slots')
                    ->label('Maximum slots')
                    ->numeric()
                    ->minValue(1)
                    ->default(16)
                    ->required(),
                TextInput::make('price')
                    ->label('Entry fee')
                    ->numeric()
                    ->prefix('₱')
                    ->minValue(0)
                    ->default(0)
                    ->helperText('Enter 0 for a free event.')
                    ->required(),
                Select::make('status')
                    ->options([
                        'open'      => 'Open',
                        'closed'    => 'Closed',
                        'cancelled' => 'Cancelled',
                        'completed' => 'Completed',
                    ])
                    ->default('open')
                    ->required(),
                Textarea::make('description')
                    ->rows(4)
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('events')
                    ->columnSpanFull(),
                FileUpload::make('result_image')
                    ->label('Result photo')
                    ->helperText('Winners poster or group photo, shown on the event page under Results.')
                    ->image()
                    ->disk('public')
                    ->directory('event-results')
                    ->imagePreviewHeight('160')
                    ->columnSpanFull(),
            ]);
    }
}