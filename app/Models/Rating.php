<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    protected $fillable = ['reservation_id', 'user_id', 'stars', 'comment'];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }
}