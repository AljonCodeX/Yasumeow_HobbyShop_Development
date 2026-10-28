<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Illuminate\Console\Command;
use App\Support\AdminAlert;

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

            if ($reservation->refresh()->status === 'cancelled') {
                AdminAlert::send(
                    'Reservation auto-cancelled',
                    'Reservation ' . $reservation->reference . ' was not paid in 30 minutes. Stock returned.',
                    'heroicon-o-x-circle',
                    'danger'
                );
            }
        }

        $late = Reservation::whereIn('status', ['pending', 'ready'])
            ->where('payment_method', 'store')
            ->whereNotNull('pickup_by')
            ->where('pickup_by', '<', now())
            ->get();

        foreach ($late as $reservation) {
            $reservation->expireAndRestock();

            if ($reservation->refresh()->status === 'expired') {
                AdminAlert::send(
                    'Reservation expired',
                    'Reservation ' . $reservation->reference . ' was not picked up in time. Stock returned.',
                    'heroicon-o-calendar-x-mark',
                    'warning'
                );
            }
        }

        $this->info($unpaid->count() . ' unpaid cancelled, ' . $late->count() . ' expired.');

        return self::SUCCESS;
    }
}