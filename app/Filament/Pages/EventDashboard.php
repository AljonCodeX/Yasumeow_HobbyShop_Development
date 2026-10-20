<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\EventStats;
use App\Filament\Widgets\RecentRegistrations;
use App\Filament\Widgets\UpcomingEvents;
use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class EventDashboard extends BaseDashboard
{
    protected static string $routePath = 'event-dashboard';

    protected static ?string $title = 'Event Dashboard';

    protected static ?string $navigationLabel = 'Event Dashboard';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static ?int $navigationSort = 0;

    public function getWidgets(): array
    {
        return [EventStats::class, UpcomingEvents::class, RecentRegistrations::class];
    }
}