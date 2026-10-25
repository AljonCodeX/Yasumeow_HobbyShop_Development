<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\Rating;
use App\Models\Registration;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EventStats extends StatsOverviewWidget
{
    protected static bool $isDiscovered = false;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $upcoming = Event::upcoming()->count();

        $completed = Event::where('status', 'completed')->count();

        $ratings = Rating::whereNotNull('event_id');
        $rated = $ratings->count();
        $satisfaction = $rated ? round((float) $ratings->avg('stars') / 5 * 100) : null;

        // Players registered in 2 or more events, as a share of all players who ever registered
        $players = Registration::where('status', 'registered')->distinct()->count('user_id');

        $repeat = Registration::where('status', 'registered')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) >= 2')
            ->pluck('user_id')
            ->count();

        $repeatRate = $players ? round($repeat / $players * 100) : null;

        return [
            Stat::make('Upcoming Events', $upcoming)
                ->description('Coming up')
                ->descriptionIcon(Heroicon::OutlinedCalendarDays, IconPosition::After)
                ->color('info'),
            Stat::make('Events Completed', $completed)
                ->description('Finished events')
                ->descriptionIcon(Heroicon::OutlinedCheckCircle, IconPosition::After)
                ->color('success'),
            Stat::make('Event Satisfaction', $satisfaction === null ? '—' : $satisfaction . '%')
                ->description($rated ? "$rated rating" . ($rated > 1 ? 's' : '') : 'No ratings yet')
                ->descriptionIcon(Heroicon::OutlinedStar, IconPosition::After)
                ->color('warning'),
            Stat::make('Repeat Players Rate', $repeatRate === null ? '—' : $repeatRate . '%')
                ->description($players ? "$repeat of $players players" : 'No players yet')
                ->descriptionIcon(Heroicon::OutlinedUserGroup, IconPosition::After)
                ->color('danger'),
        ];
    }
}