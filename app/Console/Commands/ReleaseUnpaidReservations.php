<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Illuminate\Console\Command;

class ReleaseUnpaidReservations extends Command
{
    protected $signature = 'reservations:release-unpaid';

    protected $description = 'Cancel unpaid online reservations (30 min) and expire pay-in-store reservations not picked up in time';

    public function handle(): int
    {
        $unpaid = Reservation::where('status', 'awaiting_payment')
            ->where('created_at', '<', now()->subMinutes(30))
            ->get();

        foreach ($unpaid as $reservation) {
            $reservation->cancelAndRestock();
        }

        $late = Reservation::where('status', 'pending')
            ->where('payment_method', 'store')
            ->whereNotNull('pickup_by')
            ->where('pickup_by', '<', now())
            ->get();

        foreach ($late as $reservation) {
            $reservation->expireAndRestock();
        }

        $this->info($unpaid->count() . ' unpaid cancelled, ' . $late->count() . ' expired.');

        return self::SUCCESS;
    }
}