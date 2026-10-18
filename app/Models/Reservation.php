<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Reservation extends Model
{
    protected $fillable = [
    'user_id', 'reserved_at', 'payment_method', 'status',
    'amount_total', 'amount_due', 'amount_paid', 'paymongo_session_id',
    'contact_name', 'contact_phone', 'note',
    ];

    protected function casts(): array
    {
        return [
            'reserved_at'  => 'datetime',
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

    // Cancels an unpaid reservation and gives the held stock back
    public function cancelAndRestock(): void
    {
        DB::transaction(function () {
            $reservation = self::lockForUpdate()->find($this->id);

            if (! $reservation || $reservation->status !== 'awaiting_payment') {
                return;
            }

            foreach ($reservation->items as $item) {
                Product::where('id', $item->product_id)->increment('stock_qty', $item->quantity);
            }

            $reservation->update(['status' => 'cancelled']);
        });
    }
}