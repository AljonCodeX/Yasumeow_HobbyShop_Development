<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;

class CompletePastEvents extends Command
{
    protected $signature = 'events:complete-past';

    protected $description = 'Mark open or closed events as completed 6 hours after they start';

    public function handle(): int
    {
        $count = Event::whereIn('status', ['open', 'closed'])
            ->where('event_date', '<', now()->subHours(6))
            ->update(['status' => 'completed', 'status_changed_at' => now()]);

        $this->info($count . ' events completed.');

        return self::SUCCESS;
    }
}