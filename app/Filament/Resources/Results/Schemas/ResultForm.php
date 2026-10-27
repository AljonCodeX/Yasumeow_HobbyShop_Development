<?php

namespace App\Filament\Resources\Results\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ResultForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->label('Event')
                ->disabled()
                ->dehydrated(false),
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