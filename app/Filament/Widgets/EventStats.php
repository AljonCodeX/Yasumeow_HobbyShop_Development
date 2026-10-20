<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\Registration;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EventStats extends StatsOverviewWidget
{
    protected static bool $isDiscovered = false;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $upcoming = Event::where('event_date', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->withCount(['registrations as taken_count' => fn ($q) => $q->where('status', 'registered')])
            ->get();

        $full = $upcoming->filter(fn (Event $e) => $e->taken_count >= $e->max_slots)->count();
        $expectedFees = $upcoming->sum(fn (Event $e) => (float) $e->price * $e->taken_count);

        return [
            Stat::make('Upcoming events', $upcoming->count())
                ->color('info'),
            Stat::make('Registered players', $upcoming->sum('taken_count'))
                ->description('In upcoming events')
                ->color('success'),
            Stat::make('Full events', $full)
                ->color($full > 0 ? 'danger' : 'gray'),
            Stat::make('Expected entry fees', '₱' . number_format($expectedFees, 2))
                ->description('Paid on the day'),
            Stat::make('New sign-ups (7 days)', Registration::where('status', 'registered')
                ->where('registered_at', '>=', now()->subDays(7))->count()),
        ];
    }
}