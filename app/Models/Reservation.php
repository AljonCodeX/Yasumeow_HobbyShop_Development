<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Reservation extends Model
{
    // Days a pay-in-store reservation is held before it expires
    public const PICKUP_DAYS = 2;

    protected $fillable = [
        'user_id', 'reserved_at', 'pickup_by', 'payment_method', 'status',
        'amount_total', 'amount_due', 'amount_paid', 'paymongo_session_id',
        'contact_name', 'contact_phone', 'note',
    ];

    protected function casts(): array
    {
        return [
            'reserved_at'  => 'datetime',
            'pickup_by'    => 'datetime',
            'amount_total' => 'decimal:2',
            'amount_due'   => 'decimal:2',
            'amount_paid'  => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReservationItem::class);
    }

    // Unpaid online reservation: cancel and return stock
    public function cancelAndRestock(): void
    {
        $this->release('awaiting_payment', 'cancelled');
    }

    // Pay-in-store reservation not picked up in time: expire and return stock
    public function expireAndRestock(): void
    {
        $this->release('pending', 'expired');
    }

    private function release(string $from, string $to): void
    {
        DB::transaction(function () use ($from, $to) {
            $reservation = self::lockForUpdate()->find($this->id);

            if (! $reservation || $reservation->status !== $from) {
                return;
            }

            foreach ($reservation->items as $item) {
                Product::where('id', $item->product_id)->increment('stock_qty', $item->quantity);
            }

            $reservation->update(['status' => $to]);
        });
    }
}