@extends('layouts.app')

@section('content')
<div class="shop-page">
    <div class="container">

        <header class="shop-head">
            <div>
                <h1 class="shop-title">Events</h1>
                <p class="shop-sub">Featuring Yasumeow Community League</p>
            </div>
        </header>

        @if ($games->count() > 1)
            <nav class="shop-chips" aria-label="Filter events by game">
                <a href="{{ route('events') }}" class="shop-chip {{ $selected ? '' : 'active' }}">All</a>
                @foreach ($games as $game)
                    <a href="{{ route('events', ['game' => $game->slug]) }}"
                       class="shop-chip {{ $selected === $game->slug ? 'active' : '' }}">{{ $game->name }}</a>
                @endforeach
            </nav>
        @endif

        <p class="shop-count">{{ $events->count() }} upcoming {{ $events->count() === 1 ? 'event' : 'events' }}</p>

        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
            @forelse ($events as $event)
                @include('partials.event-item', ['event' => $event])
            @empty
                <div class="col-12">
                    <div class="shop-empty">
                        <i class="bi bi-calendar-x"></i>
                        <p class="mb-0">No upcoming events{{ $selected ? ' for this game' : '' }} yet. Check back soon!</p>
                    </div>
                </div>
            @endforelse
        </div>

        @if ($past->isNotEmpty())
            <h2 class="checkout-section-title mt-5">Past events</h2>
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
                @foreach ($past as $event)
                    @include('partials.event-item', ['event' => $event, 'ended' => true])
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection