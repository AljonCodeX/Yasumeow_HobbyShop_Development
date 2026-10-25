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
        <nav class="shop-chips" role="group" aria-label="Filter games by category">
            <button type="button" class="shop-chip active" data-filter="all">All</button>
            @foreach ($chips as $chip)
                <button type="button" class="shop-chip"
                        data-filter="{{ \Illuminate\Support\Str::slug($chip) }}">
                    {{ $chip }}
                </button>
            @endforeach
        </nav>

        {{-- All games in one grid --}}
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
            @foreach ($games as $game)
                @include('partials.game-item', [
                    'game' => $game,
                    'sectionSlug' => \Illuminate\Support\Str::slug($game->category?->name ?? 'Others'),
                ])
            @endforeach
        </div>

        @if ($games->isEmpty())
            <div class="shop-empty">
                <i class="bi bi-controller"></i>
                <p class="mb-0">No games found.</p>
            </div>
        @endif
    </div>
</div>
@endsection