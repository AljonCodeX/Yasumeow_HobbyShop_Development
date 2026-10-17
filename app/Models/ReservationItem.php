<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationItem extends Model
{
    // This table has a composite primary key (reservation_id + product_id), no single id column
    protected $primaryKey = null;

    public $incrementing = false;

    protected $fillable = ['reservation_id', 'product_id', 'quantity', 'unit_price'];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}