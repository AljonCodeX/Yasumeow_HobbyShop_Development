<?php

namespace App\Filament\Widgets;

use App\Models\Registration;
use Filament\Widgets\ChartWidget;

class RegistrationsByGame extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected static ?int $sort = 2;

    protected ?string $heading = 'Sign-ups by game';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $byGame = Registration::where('status', 'registered')
            ->with('event.game')
            ->get()
            ->countBy(fn ($r) => $r->event?->game?->name ?? 'Unknown');

        return [
            'labels' => $byGame->keys()->all(),
            'datasets' => [[
                'data' => $byGame->values()->all(),
                'backgroundColor' => ['#3b82f6', '#22c55e', '#f59e0b', '#ef4444', '#a855f7', '#14b8a6'],
            ]],
        ];
    }
}