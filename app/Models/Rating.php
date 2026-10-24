<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['user_id', 'reservation_id', 'event_id', 'stars'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}