<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Make sure the categories exist
        foreach (['Board Games', 'Card Games', 'Beyblade', 'Tamiya', 'PS5'] as $name) {
            DB::table('categories')->updateOrInsert(['name' => $name], [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2) Remove any old link from games to game_categories
        $links = collect(Schema::getForeignKeys('games'))
            ->filter(fn ($fk) => $fk['foreign_table'] === 'game_categories');

        if ($links->isNotEmpty()) {
            Schema::table('games', function (Blueprint $table) use ($links) {
                foreach ($links as $fk) {
                    $table->dropForeign($fk['name']);
                }
            });

            Schema::dropColumns('games', $links->pluck('columns')->flatten()->all());
        }

        // 3) New columns (skipped if they already exist)
        if (! Schema::hasColumn('games', 'category_id')) {
            Schema::table('games', function (Blueprint $table) {
                $table->foreignId('category_id')->nullable()->after('slug')
                    ->constrained('categories')->restrictOnDelete();
            });
        }

        if (! Schema::hasColumn('games', 'image')) {
            Schema::table('games', function (Blueprint $table) {
                $table->string('image')->nullable()->after('category_id');
            });
        }

        // 4) Move each game into a category
        $byId = DB::table('categories')->pluck('id', 'name');

        $bySlug = [
            'pokemon-tcg'          => 'Card Games',
            'unmatched'            => 'Board Games',
            'beyblade'             => 'Beyblade',
            'tamiya-mini-4wd'      => 'Tamiya',
            'marvels-spider-man-2' => 'PS5',
            'tekken-8'             => 'PS5',
        ];

        $hasSection = Schema::hasColumn('games', 'section');

        foreach (DB::table('games')->get() as $game) {
            $name = $bySlug[$game->slug]
                ?? ($hasSection && $game->section === 'PS5 Games' ? 'PS5' : 'Board Games');

            DB::table('games')->where('id', $game->id)
                ->update(['category_id' => $byId[$name] ?? $byId['Board Games']]);
        }

        // 5) Remove the old columns that still exist
        $old = array_filter(
            ['section', 'type', 'icon', 'sort_order'],
            fn ($column) => Schema::hasColumn('games', $column)
        );

        if ($old) {
            Schema::dropColumns('games', array_values($old));
        }

        // 6) The unused table
        Schema::dropIfExists('game_categories');
    }

    public function down(): void
    {
        // Not reversible: the old data is gone.
    }
};