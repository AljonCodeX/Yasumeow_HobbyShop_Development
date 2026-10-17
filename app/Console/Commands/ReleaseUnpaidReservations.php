<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Illuminate\Console\Command;

class ReleaseUnpaidReservations extends Command
{
    protected $signature = 'reservations:release-unpaid';

    protected $description = 'Cancel reservations not paid within 30 minutes and return their stock';

    public function handle(): int
    {
        $expired = Reservation::where('status', 'awaiting_payment')
            ->where('created_at', '<', now()->subMinutes(30))
            ->get();

        foreach ($expired as $reservation) {
            $reservation->cancelAndRestock();
        }

        $this->info($expired->count() . ' reservation(s) released.');

        return self::SUCCESS;
    }
}