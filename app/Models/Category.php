<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getIconAttribute(): string
    {
        return match ($this->name) {
            'Card Games'             => 'bi-suit-spade-fill',
            'Beyblade'               => 'bi-tornado',
            'Tamiya'                 => 'bi-car-front-fill',
            'PS5'                    => 'bi-controller',
            'Figures & Collectibles' => 'bi-trophy',
            'Accessories'            => 'bi-headset',
            default                  => 'bi-tag-fill',
        };
    }
}