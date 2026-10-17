@extends('layouts.app')

@section('content')
<div class="container pb-5">
    <nav class="pt-4 pb-3">
        <a href="{{ url('/games') }}" class="small fw-bold text-body text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i>Back to Games and Activities
        </a>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="game-detail-hero mb-4">
                <i class="bi {{ $game['icon'] }}"></i>
            </div>

            <span class="badge text-bg-dark mb-2">{{ $game['type'] }}</span>
            <h1 class="h3 fw-bold">{{ $game['name'] }}</h1>
            <p>{{ $game['about'] }}</p>

            <h2 class="h5 fw-bold text-uppercase mt-4 mb-3">
                <i class="bi bi-journal-text me-2"></i>Beginner Guide
            </h2>
            <ol class="list-group list-group-numbered">
                @foreach ($game['guide'] ?? [] as $step)
                    <li class="list-group-item">{{ $step }}</li>
                @endforeach
            </ol>
        </div>

        <div class="col-lg-4">
            @if (!empty($section['note']))
                <div class="games-note d-flex align-items-center gap-3 mb-3">
                    <i class="bi {{ $section['note']['icon'] }}"></i>
                    <div>
                        <strong>{{ $section['note']['title'] }}</strong>
                        <span class="d-block small">{{ $section['note']['text'] }}</span>
                    </div>
                </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="small text-muted mb-1">Category</p>
                    <p class="fw-bold mb-3">
                        <i class="bi {{ $section['icon'] }} me-1"></i>{{ $section['title'] }}
                    </p>
                    <a href="{{ url('/events') }}" class="btn btn-dark btn-sm w-100">
                        <i class="bi bi-calendar-event me-2"></i>View Events
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection