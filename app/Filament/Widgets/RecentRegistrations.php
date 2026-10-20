<?php

namespace App\Filament\Widgets;

use App\Models\Registration;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentRegistrations extends TableWidget
{
    protected static bool $isDiscovered = false;

    protected static ?int $sort = 3;

    protected static ?string $heading = 'Latest registrations';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => Registration::query()->with(['user', 'event'])->latest('registered_at')->limit(5))
            ->columns([
                TextColumn::make('user.name')->label('Player'),
                TextColumn::make('event.title')->label('Event'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'registered' ? 'success' : 'danger'),
                TextColumn::make('registered_at')->label('Registered')->dateTime('M d, g:i A'),
            ])
            ->paginated(false);
    }
}