<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\Game;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SearchController extends Controller
{
    // Full results page (Enter in the search window)
    public function index(Request $request)
    {
        $q = $this->cleanQuery($request);

        $results = mb_strlen($q) >= 2
            ? $this->find($q, 12)
            : ['products' => collect(), 'games' => collect(), 'events' => collect()];

        $total = $results['products']->count() + $results['games']->count() + $results['events']->count();

        return view('search-results', $results + ['q' => $q, 'total' => $total]);
    }

    private function cleanQuery(Request $request): string
    {
        return mb_substr(trim((string) $request->query('q', '')), 0, 60);
    }

    // Names that start with the text come first, then names that contain it,
    // then matches found only in the category or description
    private function find(string $q, int $limit): array
    {
        $like  = '%' . $q . '%';
        $start = $q . '%';

        $products = Product::with('category')
            ->where(function ($w) use ($like) {
                $w->where('name', 'like', $like)
                  ->orWhere('description', 'like', $like)
                  ->orWhereHas('category', fn ($c) => $c->where('name', 'like', $like));
            })
            ->orderByRaw('CASE WHEN name LIKE ? THEN 0 WHEN name LIKE ? THEN 1 ELSE 2 END', [$start, $like])
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->take($limit)
            ->get();

        $games = Game::with('category')
            ->where('is_active', true)
            ->where(function ($w) use ($like) {
                $w->where('name', 'like', $like)
                ->orWhere('description', 'like', $like)
                ->orWhereHas('category', fn ($c) => $c->where('name', 'like', $like));
            })
            ->orderByRaw('CASE WHEN name LIKE ? THEN 0 WHEN name LIKE ? THEN 1 ELSE 2 END', [$start, $like])
            ->orderBy('name')
            ->take($limit)
            ->get();

        // Upcoming events only
        $events = Event::with('game')
            ->withCount(['registrations as taken_count' => fn ($r) => $r->where('status', 'registered')])
            ->where('event_date', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->where(function ($w) use ($like) {
                $w->where('title', 'like', $like)
                  ->orWhereHas('game', fn ($g) => $g->where('name', 'like', $like));
            })
            ->orderBy('event_date')
            ->take($limit)
            ->get();

        return compact('products', 'games', 'events');
    }

    private function eventJson(Event $e): array
    {
        return [
            'title' => $e->title,
            'when'  => $e->event_date->format('M d, g:i A'),
            'fee'   => $e->price > 0 ? ['text' => '₱' . number_format((float) $e->price)] : ['text' => 'FREE'],
            'url'   => route('events.show', $e),
            'image' => $e->image ? asset('storage/' . $e->image) : null,
            'icon'  => $e->game?->category?->icon ?? 'bi-calendar-event',
        ];
    }

    // Everything searchable in one small list. The browser filters it, so typing needs no server calls.
    public function data()
    {
      $data = Cache::remember('search-index', 300, function () {
          return [
              'products' => Product::with('category')->get()->map(fn ($p) => [
                  'name'     => $p->name,
                  'category' => $p->category?->name ?? 'Others',
                  'price'    => '₱' . number_format((float) $p->price),
                  'url'      => route('products.show', $p),
                  'image'    => $p->image ? asset('storage/' . $p->image) : null,
                  'icon'     => $p->category?->icon ?? 'bi-box-seam',
                  'stock'    => $p->stockStatus(),
                  's'        => mb_strtolower($p->name . ' ' . ($p->category?->name ?? '') . ' ' . $p->description),
              ])->all(),

              'games' => Game::with('category')->where('is_active', true)->get()->map(fn ($g) => [
                'name'  => $g->name,
                'type'  => $g->category?->name ?? 'Game',
                'url'   => url('/games/' . $g->slug),
                'image' => $g->image ? asset('storage/' . $g->image) : null,
                'icon'  => $g->category?->icon ?? 'bi-controller',
                's'     => mb_strtolower($g->name . ' ' . ($g->category?->name ?? '') . ' ' . $g->description),
            ])->all(),

              'events' => Event::with('game.category')->upcoming()->get()->map(fn ($e) => $this->eventJson($e) + [
                  's' => mb_strtolower($e->title . ' ' . ($e->game?->name ?? '')),
              ])->all(),
          ];
      });

      return response()->json($data);
  }
}