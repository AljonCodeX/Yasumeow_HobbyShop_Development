<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'image'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function games()
    {
        return $this->hasMany(Game::class);
    }
    
    protected static function booted(): void
    {
        static::deleting(function (Category $category) {
            if (! $category->isForceDeleting()) {
                $category->name = $category->name . ' (deleted ' . $category->id . ')';
                $category->saveQuietly();
            }
        });
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
            'Board Games'            => 'bi-dice-5-fill',
            default                  => 'bi-tag-fill',
        };
    }
}