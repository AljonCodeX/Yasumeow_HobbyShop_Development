<?php

namespace App\Filament\Resources\Games\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class GameForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
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
                FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('games')
                    ->helperText('Optional. Without an image, the category icon is shown.')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Show on website')
                    ->default(true),
            ]);
    }
}