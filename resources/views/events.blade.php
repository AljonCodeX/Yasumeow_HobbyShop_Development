@extends('layouts.app')

@section('content')
<div class="container">

    <header class="games-hero mb-4">
        <div class="games-hero-text">
            <h1 class="h3 fw-bold text-uppercase mb-1">
                <i class="bi bi-calendar-event me-2"></i>Events
            </h1>
            <p class="mb-0">Tournaments, races and game nights at Yasumeow Hobby Shop. Sign up and join the fun.</p>
        </div>
    </header>

    @if ($games->count() > 1)
        <div class="games-chips mb-4" aria-label="Filter events by game">
            <a href="{{ route('events') }}" class="games-chip {{ $selected ? '' : 'active' }}">All</a>
            @foreach ($games as $game)
                <a href="{{ route('events', ['game' => $game->slug]) }}"
                   class="games-chip {{ $selected === $game->slug ? 'active' : '' }}">{{ $game->name }}</a>
            @endforeach
        </div>
    @endif

    <section>
        <h2 class="h5 fw-bold text-uppercase mb-3">
            <i class="bi bi-calendar-check me-2"></i>Upcoming Events
        </h2>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3">
            @forelse ($events as $event)
                <div class="col">
                    @include('partials.event-card', ['event' => $event])
                </div>
            @empty
                <div class="col-12">
                    <div class="home-empty">
                        <i class="bi bi-calendar-x"></i>
                        <p class="mb-0">No upcoming events{{ $selected ? ' for this game' : '' }} yet. Check back soon!</p>
                    </div>
                </div>
            @endforelse
        </div>
    </section>

    @if ($past->isNotEmpty())
        <section class="pt-5 pb-4">
            <h2 class="h5 fw-bold text-uppercase mb-3">
                <i class="bi bi-clock-history me-2"></i>Past Events
            </h2>
            <div class="list-group shadow-sm">
                @foreach ($past as $event)
                    <a href="{{ route('events.show', $event) }}"
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span>
                            <strong>{{ $event->title }}</strong>
                            <small class="d-block text-muted">{{ $event->game?->name }}</small>
                        </span>
                        <small class="text-muted">{{ $event->event_date->format('M d, Y') }}</small>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection