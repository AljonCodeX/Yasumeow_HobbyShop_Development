<?php

namespace App\Http\Controllers;

use App\Models\Game;

class PageController extends Controller
{
    // Display info for each section (order here = order on the page)
    private function sectionMeta(): array
    {
        return [
            'Board Games' => [
                'title' => 'Board Games',
                'icon'  => 'bi-dice-5-fill',
            ],
            'Physical Toy Games' => [
                'title' => 'Physical Toy Games',
                'icon'  => 'bi-puzzle-fill',
            ],
            'PS5 Games' => [
                'title' => 'PS5 Games',
                'icon'  => 'bi-controller',
                'note'  => [
                    'icon'  => 'bi-clock-history',
                    'title' => 'PS5 Rental',
                    'text'  => '₱50 per hour for up to 2 players. Pick a game below and play on the shop PS5.',
                ],
            ],
        ];
    }

    public function games()
    {
        $games = Game::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('section');

        $sections = [];
        foreach ($this->sectionMeta() as $key => $meta) {
            if ($games->has($key)) {
                $sections[] = $meta + ['games' => $games[$key]];
            }
        }

        return view('games', ['sections' => $sections]);
    }

    public function gameDetails(string $slug)
    {
        $game = Game::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $section = $this->sectionMeta()[$game->section] ?? [
            'title' => $game->section,
            'icon'  => 'bi-controller',
        ];

        return view('game-details', [
            'game'    => $game,
            'section' => $section,
        ]);
    }
}