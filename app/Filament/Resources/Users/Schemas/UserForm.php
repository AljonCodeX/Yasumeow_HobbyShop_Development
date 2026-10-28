<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('email')->disabled()->dehydrated(false),
            TextInput::make('phone')->maxLength(20),
            Toggle::make('is_admin')
                ->label('Admin')
                ->disabled(fn ($record) => $record?->id === Auth::id())
                ->dehydrated(fn ($record) => $record?->id !== Auth::id())
                ->helperText(fn ($record) => $record?->id === Auth::id()
                    ? 'You cannot remove your own admin access.'
                    : null),
        ]);
    }
}