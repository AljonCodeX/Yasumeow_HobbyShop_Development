@extends('layouts.app')

@section('content')
<div class="container">

    {{-- Banner + search --}}
    <header class="games-hero mb-4">
        <div class="games-hero-text">
            <h1 class="h3 fw-bold text-uppercase mb-1">
                <i class="bi bi-controller me-2"></i>Games and Activities
            </h1>
            <p class="mb-0">Pick a game, learn how to play, then come join the fun at Yasumeow Hobby Shop.</p>
        </div>
    </header>

    {{-- Filter chips --}}
    <div class="games-chips mb-4" role="group" aria-label="Filter games by category">
        <button type="button" class="games-chip active" data-filter="all">All</button>
        @foreach ($sections as $section)
            <button type="button" class="games-chip" data-filter="{{ \Illuminate\Support\Str::slug($section['title']) }}">
                <i class="bi {{ $section['icon'] }} me-1"></i>{{ $section['title'] }}
            </button>
        @endforeach
    </div>

    {{-- Sections --}}
    @foreach ($sections as $section)
        @php($sectionSlug = \Illuminate\Support\Str::slug($section['title']))

        <section class="games-section">
            <h2 class="h5 fw-bold text-uppercase mb-3">
                <i class="bi {{ $section['icon'] }} me-2"></i>{{ $section['title'] }}
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

            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3">
                @foreach ($section['games'] as $game)
                    <div class="col game-col"
                         data-section="{{ $sectionSlug }}"
                         data-search="{{ \Illuminate\Support\Str::lower($game['name'].' '.$game['type'].' '.$game['description']) }}">
                        <a href="{{ url('/games/' . $game['slug']) }}" class="game-card card h-100 border-0 shadow-sm overflow-hidden text-decoration-none">
                            <div class="game-card-cover">
                                <span class="game-card-badge">{{ $game['type'] }}</span>
                                <i class="bi {{ $game['icon'] }}"></i>
                            </div>
                            <div class="card-body">
                                <h3 class="h6 fw-bold text-body mb-2">{{ $game['name'] }}</h3>
                                <p class="small text-muted mb-0 game-card-desc">{{ $game['description'] }}</p>
                            </div>
                            <div class="card-footer bg-white border-0 pt-0 pb-3">
                                <span class="game-card-link">View Game Details <i class="bi bi-arrow-right ms-1"></i></span>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </section>
    @endforeach

    {{-- Empty state --}}
    <div id="gamesEmpty" class="games-empty text-center d-none">
        <i class="bi bi-emoji-frown"></i>
        <p class="fw-bold mb-1">No games found</p>
        <p class="small text-muted mb-0">Try a different search or pick another category.</p>
    </div>
</div>
@endsection