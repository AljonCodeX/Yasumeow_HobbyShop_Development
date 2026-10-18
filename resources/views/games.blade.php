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
            @foreach ($sections as $section)
                <button type="button" class="shop-chip"
                        data-filter="{{ \Illuminate\Support\Str::slug($section['title']) }}">
                    {{ $section['title'] }}
                </button>
            @endforeach
        </nav>

        {{-- Sections --}}
        @foreach ($sections as $section)
            @php($sectionSlug = \Illuminate\Support\Str::slug($section['title']))

            <section class="games-section">
                <h2 class="checkout-section-title">
                    <i class="bi {{ $section['icon'] }} me-1"></i>{{ $section['title'] }}
                </h2>

                @if (!empty($section['note']))
                    <div class="games-note d-flex align-items-center gap-3 mb-3">
                        <i class="bi {{ $section['note']['icon'] }}"></i>
                        <div>
                            <strong>{{ $section['note']['title'] }}</strong>
                            <span class="d-block small">{{ $section['note']['text'] }}</span>
                        </div>
                    </div>
                @endif

                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
                    @foreach ($section['games'] as $game)
                        @include('partials.game-item', ['game' => $game, 'sectionSlug' => $sectionSlug])
                    @endforeach
                </div>
            </section>
        @endforeach

        {{-- Empty state --}}
        <div id="gamesEmpty" class="shop-empty d-none">
            <i class="bi bi-controller"></i>
            <p class="mb-0">No games found.</p>
        </div>
    </div>
</div>
@endsection