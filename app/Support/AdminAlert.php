<?php

namespace App\Support;

use App\Models\User;
use Filament\Notifications\Notification;

class AdminAlert
{
    // Sends a bell notification to every admin. Never breaks the page if it fails.
    public static function send(string $title, string $body, string $icon = 'heroicon-o-bell', string $color = 'info'): void
    {
        try {
            $admins = User::where('is_admin', true)->get();

            if ($admins->isEmpty()) {
                return;
            }

            Notification::make()
                ->title($title)
                ->body($body)
                ->icon($icon)
                ->iconColor($color)
                ->sendToDatabase($admins);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}