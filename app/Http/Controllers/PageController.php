<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\Game;
use App\Models\Product;

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

    public function home()
    {
        $events = Event::with('game')
            ->withCount(['registrations as taken_count' => function ($q) {
                $q->where('status', 'registered');
            }])
            ->upcoming()
            ->take(4)
            ->get();

        $games = Game::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->take(4)
            ->get();

        $categories = Category::orderBy('name')->get();

        // Featured products first, then newest
        $products = Product::with('category')
            ->orderByDesc('is_featured')
            ->latest()
            ->take(5)
            ->get();

        return view('home', compact('events', 'games', 'categories', 'products'));
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