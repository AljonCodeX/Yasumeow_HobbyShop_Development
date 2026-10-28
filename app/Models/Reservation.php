<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
            'picked_up_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Reference like 20261028-001: the date, then that day's number
        static::creating(function (Reservation $reservation) {
            $prefix = now()->format('Ymd');

            // Lock works because checkout creates the reservation inside a transaction
            $last = DB::table('reservations')
                ->where('reference', 'like', $prefix . '-%')
                ->lockForUpdate()
                ->orderByDesc('reference')
                ->value('reference');

            $next = $last ? ((int) substr($last, 9)) + 1 : 1;

            $reservation->reference = $prefix . '-' . str_pad($next, 3, '0', STR_PAD_LEFT);
        });
        
        // Remember when the status last changed (not on creation)
        static::saving(function (Reservation $reservation) {
        if ($reservation->exists && $reservation->isDirty('status')) {
            $reservation->status_changed_at = now();

            if ($reservation->status === 'picked_up') {
                $reservation->picked_up_at = now();
            }
        }
    });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReservationItem::class);
    }

    public function rating(): HasOne
    {
        return $this->hasOne(Rating::class);
    }

    // Unpaid online reservation: cancel and return stock
    public function cancelAndRestock(): void
    {
        $this->release(['awaiting_payment'], 'cancelled');
    }

    // Pay-in-store reservation not picked up in time: expire and return stock
    public function expireAndRestock(): void
    {
        $this->release(['pending', 'ready'], 'expired');
    }

    // Staff cancels from the admin panel
    public function staffCancel(): void
    {
        $this->release(['awaiting_payment', 'pending', 'ready'], 'cancelled');
    }

    // Customer paid after the reservation was auto-cancelled.
    // Returns 'recovered', 'refund', or null (nothing to do).
    public function recoverLatePayment(): ?string
    {
        return DB::transaction(function () {
            $reservation = self::lockForUpdate()->find($this->id);

            // Only online reservations that were cancelled and never marked as paid
            if (! $reservation
                || $reservation->status !== 'cancelled'
                || $reservation->payment_method !== 'online'
                || (float) $reservation->amount_paid > 0) {
                return null;
            }

            $items = $reservation->items;

            $products = Product::whereIn('id', $items->pluck('product_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $enough = $items->every(fn ($i) => $products->has($i->product_id)
                && $products[$i->product_id]->stock_qty >= $i->quantity);

            // Not enough stock: stay cancelled, but record the money that was taken
            if (! $enough) {
                $reservation->update(['amount_paid' => $reservation->amount_due]);

                return 'refund';
            }

            foreach ($items as $item) {
                $products[$item->product_id]->decrement('stock_qty', $item->quantity);
            }

            $reservation->update([
                'status'      => 'pending',
                'amount_paid' => $reservation->amount_due,
            ]);

            return 'recovered';
        });
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

    // Pay in store: Reserved or Ready. QR Ph: only while still unpaid.
    public function canCustomerCancel(): bool
    {
        if ($this->status === 'awaiting_payment') {
            return true;
        }

        return in_array($this->status, ['pending', 'ready'], true)
            && $this->payment_method === 'store';
    }

    // Customer cancels from the website: items go back to stock
    public function customerCancel(): void
    {
        $from = $this->payment_method === 'store' ? ['pending', 'ready'] : ['awaiting_payment'];

        $this->release($from, 'cancelled');
    }

    public function scopeDashboardFilters($query, ?array $f, string $dateColumn = 'reserved_at'): void
    {
        $query
            ->when($f['startDate'] ?? null, fn ($q, $d) => $q->whereDate($dateColumn, '>=', $d))
            ->when($f['endDate'] ?? null, fn ($q, $d) => $q->whereDate($dateColumn, '<=', $d))
            ->when($f['status'] ?? null, fn ($q, $s) => $q->whereIn('status', $s === 'cancelled' ? ['cancelled', 'expired'] : [$s]))
            ->when($f['category'] ?? null, fn ($q, $c) => $q->whereHas('items.product', fn ($p) => $p->where('category_id', $c)));
    }
}