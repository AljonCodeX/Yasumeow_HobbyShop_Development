<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'name', 'slug', 'section', 'type', 'icon',
        'description', 'about', 'guide', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'guide' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}