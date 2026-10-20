<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Events\EventResource;
use App\Models\Event;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class UpcomingEvents extends TableWidget
{
    protected static bool $isDiscovered = false;

    protected static ?int $sort = 3;

    protected static ?string $heading = 'Upcoming events';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => Event::query()
                ->with('game')
                ->withCount(['registrations as taken_count' => fn ($q) => $q->where('status', 'registered')])
                ->upcoming()
                ->limit(5))
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('game.name')->label('Game'),
                TextColumn::make('event_date')->label('Date and time')->dateTime('M d, g:i A'),
                TextColumn::make('slots')
                    ->label('Slots')
                    ->state(fn (Event $e) => $e->taken_count . ' / ' . $e->max_slots),
                TextColumn::make('price')->label('Fee')->money('PHP'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open'   => 'success',
                        'closed' => 'warning',
                        default  => 'gray',
                    }),
            ])
            ->recordUrl(fn (Event $e) => EventResource::getUrl('edit', ['record' => $e]))
            ->paginated(false);
    }
}