<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Game extends Model
{
    protected $fillable = [
        'name', 'slug', 'category_id', 'image',
        'description', 'about', 'guide', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'guide' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // The slug is made from the name, so the admin never types it
        static::saving(function (Game $game) {
            if (! $game->slug || $game->isDirty('name')) {
                $slug = Str::slug($game->name);
                $base = $slug;
                $i = 2;

                while (static::where('slug', $slug)->where('id', '!=', $game->id ?? 0)->exists()) {
                    $slug = $base . '-' . $i++;
                }

                $game->slug = $slug;
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}