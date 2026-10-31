@extends('layouts.app')

@section('content')
<div class="shop-page">
    <div class="container">

        <header class="shop-head">
            <div>
                <h1 class="shop-title">Games and Activities</h1>
                <p class="shop-sub">Pick a game, learn how to play, then come join the fun.</p>
            </div>
        </header>

        {{-- Filter chips --}}
        @if ($categories->count() > 1)
            <nav class="shop-chips" aria-label="Filter games by category">
                <a href="{{ route('games') }}"
                   class="shop-chip {{ $activeCategory ? '' : 'active' }}">All</a>
                @foreach ($categories as $category)
                    <a href="{{ route('games', ['category' => $category->id]) }}"
                       class="shop-chip {{ $activeCategory === $category->id ? 'active' : '' }}">{{ $category->name }}</a>
                @endforeach
            </nav>
        @endif

        <p class="shop-count">{{ $games->total() }} {{ $games->total() === 1 ? 'game' : 'games' }}</p>

        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
            @forelse ($games as $game)
                @include('partials.game-item', [
                    'game' => $game,
                    'sectionSlug' => \Illuminate\Support\Str::slug($game->category?->name ?? 'Others'),
                ])
            @empty
                <div class="col-12">
                    <div class="shop-empty">
                        <i class="bi bi-controller"></i>
                        <p class="mb-0">No games found.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-4">{{ $games->links() }}</div>
    </div>
</div>
@endsection