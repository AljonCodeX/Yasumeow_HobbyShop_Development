@extends('layouts.app')

@section('content')
<div class="container">
    <header class="pt-4 pb-3">
        <h1 class="h3 fw-bold text-uppercase mb-1">
            <i class="bi bi-controller me-2"></i>Games and Activities
        </h1>
        <p class="text-muted mb-0">Pick a game and see what's happening at Yasumeow Hobby Shop.</p>
    </header>

    @foreach ($sections as $section)
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
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm overflow-hidden">
                            <div class="event-card-img">
                                <i class="bi {{ $game['icon'] }}"></i>
                            </div>
                            <div class="card-body">
                                <h3 class="h6 fw-bold">{{ $game['name'] }}</h3>
                                <span class="badge text-bg-dark mb-2">{{ $game['type'] }}</span>
                                <p class="small text-muted mb-0">{{ $game['description'] }}</p>
                            </div>
                            <div class="card-footer bg-white border-0 pt-0 pb-3">
                                <a href="{{ url('/games/' . $game['slug']) }}" class="btn btn-dark btn-sm w-100">View Game Details</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endforeach
</div>
@endsection