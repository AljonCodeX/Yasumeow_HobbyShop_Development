<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\Game;
use App\Models\Product;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use App\Models\Rating;
use App\Models\Registration;
use Illuminate\Http\Request;
use App\Models\Result;

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
            'Hobby Toys' => [
                'title' => 'Hobby Toys',
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

        $games = Game::with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->take(4)
            ->get();

        $categories = Category::orderBy('name')->get();

        // Featured products first, then newest
        $products = Product::with('category')->withSold()
            ->orderByDesc('is_featured')
            ->latest()
            ->take(8)
            ->get();

        // Popup: newest picked-up order with no rating, else newest finished event with no rating
        $toRate = null;
        $toRateEvent = null;

        if (Auth::check()) {
            $userId = Auth::id();

            $toRate = Reservation::with('items.product')
                ->where('user_id', $userId)
                ->where('status', 'picked_up')
                ->doesntHave('rating')
                ->latest('reserved_at')
                ->first();

            if (! $toRate) {
                $toRateEvent = Event::where('status', 'completed')
                    ->whereHas('results', fn ($q) => $q->where('user_id', $userId))
                    ->whereNotIn('id', Rating::where('user_id', $userId)
                        ->whereNotNull('event_id')
                        ->select('event_id'))
                    ->orderByDesc('event_date')
                    ->first();
            }
        }

        // Top players: total points across all events
        $topPlayers = Result::select('user_id')
            ->selectRaw('SUM(points) as total_points')
            ->selectRaw('SUM(placement = 1) as wins')
            ->selectRaw('COUNT(*) as events_played')
            ->with('user')
            ->groupBy('user_id')
            ->orderByDesc('total_points')
            ->orderByDesc('wins')
            ->orderBy('user_id')
            ->take(10)
            ->get();

        return view('home', compact('events', 'games', 'categories', 'products', 'toRate', 'toRateEvent', 'topPlayers'));
    }

    // Display info for a category section on the Games pages
    private function sectionFor(string $name, ?Category $category = null): array
    {
        $meta = [
            'title' => $name,
            'icon'  => $category?->icon ?? 'bi-tag-fill',
        ];

        if ($name === 'PS5') {
            $meta['note'] = [
                'icon'  => 'bi-clock-history',
                'title' => 'PS5 Rental',
                'text'  => '₱50 per hour for up to 2 players. Pick a game below and play on the shop PS5.',
            ];
        }

        return $meta;
    }

    public function games(Request $request)
    {
        // Filter chips: only categories that have an active game
        $categories = Category::whereIn('id', Game::where('is_active', true)->select('category_id'))
            ->orderBy('name')
            ->get();

        $activeCategory = $request->integer('category') ?: null;

        $games = Game::with('category')
            ->where('is_active', true)
            ->when($activeCategory, fn ($q) => $q->where('category_id', $activeCategory))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('games', compact('games', 'categories', 'activeCategory'));
    }

    public function gameDetails(string $slug)
    {
        $game = Game::with('category')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $section = $this->sectionFor($game->category?->name ?? 'Others', $game->category);

        // Next 3 upcoming events for this game
        $events = $game->events()
            ->withCount(['registrations as taken_count' => fn ($q) => $q->where('status', 'registered')])
            ->upcoming()
            ->take(3)
            ->get();

        // Products from the same category: in stock first, then featured, then newest
        $products = $game->category_id
            ? Product::with('category')->withSold()
                ->where('category_id', $game->category_id)
                ->orderByRaw('stock_qty <= 0')
                ->orderByDesc('is_featured')
                ->latest()
                ->take(4)
                ->get()
            : collect();

        return view('game-details', compact('game', 'section', 'events', 'products'));
    }

    public function about()
    {
        // Numbers that are 0 are hidden
        $stats = collect([
            ['icon' => 'bi-controller',   'value' => Game::where('is_active', true)->count(),                            'label' => 'Games to play'],
            ['icon' => 'bi-trophy-fill',  'value' => Event::where('status', 'completed')->count(),                       'label' => 'Events held'],
            ['icon' => 'bi-people-fill',  'value' => Registration::where('status', 'registered')->distinct()->count('user_id'), 'label' => 'Players joined'],
        ])->filter(fn ($s) => $s['value'] > 0);

        return view('about', ['stats' => collect()]);
    }
}