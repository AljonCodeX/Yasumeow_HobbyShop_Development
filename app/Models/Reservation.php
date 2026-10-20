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
        $this->release(['awaiting_payment'], 'cancelled');
    }

    // Pay-in-store reservation not picked up in time: expire and return stock
    public function expireAndRestock(): void
    {
        $this->release(['pending'], 'expired');
    }

    // Staff cancels from the admin panel
    public function staffCancel(): void
    {
        $this->release(['awaiting_payment', 'pending', 'ready'], 'cancelled');
    }

    private function release(array $from, string $to): void
    {
        DB::transaction(function () use ($from, $to) {
            $reservation = self::lockForUpdate()->find($this->id);

            if (! $reservation || ! in_array($reservation->status, $from, true)) {
                return;
            }

            foreach ($reservation->items as $item) {
                Product::where('id', $item->product_id)->increment('stock_qty', $item->quantity);
            }

            $reservation->update(['status' => $to]);
        });
    }

    public function scopeDashboardFilters($query, ?array $f): void
    {
        $query
            ->when($f['startDate'] ?? null, fn ($q, $d) => $q->whereDate('reserved_at', '>=', $d))
            ->when($f['endDate'] ?? null, fn ($q, $d) => $q->whereDate('reserved_at', '<=', $d))
            ->when($f['status'] ?? null, fn ($q, $s) => $q->whereIn('status', $s === 'cancelled' ? ['cancelled', 'expired'] : [$s]))
            ->when($f['category'] ?? null, fn ($q, $c) => $q->whereHas('items.product', fn ($p) => $p->where('category_id', $c)));
    }
}