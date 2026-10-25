<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'game_id', 'title', 'description', 'event_date',
        'max_slots', 'price', 'image', 'status',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'datetime',
            'price' => 'decimal:2',
        ];
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    // Events that haven't happened yet and aren't cancelled, soonest first
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('event_date', '>=', now())
            ->whereNotIn('status', ['cancelled', 'completed'])
            ->orderBy('event_date');
    }

    public function slotsLeft(): int
    {
        $taken = $this->taken_count
            ?? $this->registrations()->where('status', 'registered')->count();

        return max(0, $this->max_slots - $taken);
    }
}