<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportShopSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/shop.json');

        if (! file_exists($path)) {
            $this->command->error('shop.json not found. Run php artisan shop:export locally first.');

            return;
        }

        $data = json_decode(file_get_contents($path), true);

        Schema::disableForeignKeyConstraints();

        // Order matters for the inserts: categories, games, products, events
        foreach (['categories', 'games', 'products', 'events'] as $table) {
            DB::table($table)->truncate();

            foreach (array_chunk($data[$table] ?? [], 100) as $chunk) {
                DB::table($table)->insert($chunk);
            }

            $this->command->info($table . ': ' . count($data[$table] ?? []) . ' rows imported');
        }

        Schema::enableForeignKeyConstraints();

        Cache::forget('search-index');
    }
}