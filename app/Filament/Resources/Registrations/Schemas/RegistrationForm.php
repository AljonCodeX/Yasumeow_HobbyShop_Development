<?php

namespace App\Filament\Resources\Registrations\Schemas;

use App\Models\Event;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class RegistrationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('event_id')
                ->label('Event')
                ->relationship('event', 'title')
                ->searchable()
                ->preload()
                ->required()
                ->rules([
                    fn (Get $get, $record) => function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                        if ($get('status') !== 'registered') {
                            return;
                        }

                        $event = Event::find($value);

                        if (! $event) {
                            return;
                        }

                        $taken = $event->registrations()
                            ->where('status', 'registered')
                            ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                            ->count();

                        if ($taken >= $event->max_slots) {
                            $fail('This event is full.');
                        }
                    },
                ]),
            Select::make('user_id')
                ->label('Player')
                ->relationship('user', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->unique(
                    ignoreRecord: true,
                    modifyRuleUsing: fn (Unique $rule, Get $get) => $rule->where('event_id', $get('event_id')),
                    // message shown on duplicate
                )
                ->validationMessages(['unique' => 'This player already has a registration for this event. Edit that one instead.']),
            Select::make('status')->options([
                'registered' => 'Registered',
                'cancelled'  => 'Cancelled',
            ])->default('registered')->required(),
        ]);
    }
}