<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Card Games', 'Beyblade', 'Tamiya', 'PS5',
            'Figures & Collectibles', 'Accessories', 'Others',
        ];

        foreach ($names as $name) {
            Category::firstOrCreate(['name' => $name]);
        }

        $products = [
            ['category' => 'Card Games',             'name' => 'Pokemon TCG Booster Box', 'price' => 2500, 'stock' => 12, 'featured' => true],
            ['category' => 'Beyblade',               'name' => 'Beyblade X Starter Set',  'price' => 850,  'stock' => 3,  'featured' => true],
            ['category' => 'Tamiya',                 'name' => 'Tamiya Mini 4WD',         'price' => 1500, 'stock' => 8,  'featured' => true],
            ['category' => 'PS5',                    'name' => 'PS5 Game - Spider-Man 2', 'price' => 2495, 'stock' => 0,  'featured' => true],
            ['category' => 'Figures & Collectibles', 'name' => 'Naruto Figure',           'price' => 1200, 'stock' => 6,  'featured' => true],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(
                ['name' => $p['name']],
                [
                    'category_id' => Category::where('name', $p['category'])->value('id'),
                    'description' => 'Available at Yasumeow Hobby Shop.',
                    'price'       => $p['price'],
                    'stock_qty'   => $p['stock'],
                    'is_featured' => $p['featured'],
                ]
            );
        }
    }
}