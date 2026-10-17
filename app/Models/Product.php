<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'stock_qty',
        'image',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_featured' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // 'in', 'low' (5 or fewer left) or 'out'
    public function stockStatus(): string
    {
        return match (true) {
            $this->stock_qty <= 0 => 'out',
            $this->stock_qty <= 5 => 'low',
            default => 'in',
        };
    }
}