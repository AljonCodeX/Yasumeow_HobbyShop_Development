<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ExportShopData extends Command
{
    protected $signature = 'shop:export';

    protected $description = 'Export categories, games, products and events to database/seeders/data/shop.json';

    public function handle(): int
    {
        $data = [];

        foreach (['categories', 'games', 'products', 'events'] as $table) {
            $data[$table] = DB::table($table)
                ->orderBy('id')
                ->get()
                ->map(fn ($row) => (array) $row)
                ->all();

            $this->info($table . ': ' . count($data[$table]) . ' rows');
        }

        File::ensureDirectoryExists(database_path('seeders/data'));

        File::put(
            database_path('seeders/data/shop.json'),
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        $this->info('Saved to database/seeders/data/shop.json');

        return self::SUCCESS;
    }
}