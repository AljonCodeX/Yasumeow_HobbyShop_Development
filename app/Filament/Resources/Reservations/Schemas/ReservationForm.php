<?php

namespace App\Filament\Resources\Reservations\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ReservationForm
{
    public static function configure(Schema $schema): Schema
    {
        // Read-only details. Status changes use the buttons in the list, so stock is always handled.
        return $schema->components([
            TextInput::make('contact_name')->label('Customer')->disabled()->dehydrated(false),
            TextInput::make('contact_phone')->label('Phone')->disabled()->dehydrated(false),
            TextInput::make('status')->disabled()->dehydrated(false),
            TextInput::make('payment_method')->disabled()->dehydrated(false),
            TextInput::make('amount_total')->label('Total')->prefix('₱')->disabled()->dehydrated(false),
            TextInput::make('amount_paid')->label('Paid')->prefix('₱')->disabled()->dehydrated(false),
            TextInput::make('items_summary')
                ->label('Items')
                ->disabled()
                ->dehydrated(false)
                ->columnSpanFull()
                ->afterStateHydrated(fn ($component, $record) => $component->state(
                    $record?->items->map(fn ($i) => ($i->product?->name ?? 'Product') . ' × ' . $i->quantity)->implode(', ')
                )),
            Textarea::make('note')->disabled()->dehydrated(false)->columnSpanFull(),
        ]);
    }
}