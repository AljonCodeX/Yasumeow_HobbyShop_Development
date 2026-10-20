<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    public function getHeading(): string|Htmlable
    {
        return 'Welcome Back, ' . Auth::user()?->name . '!';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return null;
    }

    public function getWidgets(): array
    {
        return [];
    }
}