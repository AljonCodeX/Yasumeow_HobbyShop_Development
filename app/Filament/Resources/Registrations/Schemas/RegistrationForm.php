<?php

namespace App\Filament\Resources\Registrations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class RegistrationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('event_id')->label('Event')->relationship('event', 'title')->searchable()->preload()->required(),
            Select::make('user_id')->label('Player')->relationship('user', 'name')->searchable()->preload()->required(),
            Select::make('status')->options([
                'registered' => 'Registered',
                'cancelled'  => 'Cancelled',
            ])->default('registered')->required(),
        ]);
    }
}