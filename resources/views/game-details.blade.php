@extends('layouts.app')

@section('content')
<div class="shop-page">
    <div class="container">

        <a href="{{ url('/games') }}" class="product-back">
            <i class="bi bi-arrow-left me-1"></i>Back to Games and Activities
        </a>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="game-detail-hero overflow-hidden mb-4">
                    @if ($game->image)
                        <img src="{{ asset('storage/' . $game->image) }}" alt="{{ $game->name }}" class="w-100 h-100 object-fit-cover">
                    @else
                        <i class="bi {{ $section['icon'] }}"></i>
                    @endif
                </div>

                <span class="shop-item-cat">{{ $section['title'] }}</span>
                <h1 class="product-name">{{ $game->name }}</h1>
                <p>{{ $game->about }}</p>

                <h2 class="checkout-section-title mt-4">
                    <i class="bi bi-journal-text me-1"></i>Beginner guide
                </h2>
                <ol class="game-steps">
                    @foreach ($game->guide ?? [] as $step)
                        <li>{{ $step }}</li>
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

                <div class="event-card">
                    <h2 class="checkout-section-title">Upcoming events</h2>

                    @forelse ($events as $event)
                        @php $left = $event->slotsLeft(); @endphp
                        <a href="{{ route('events.show', $event) }}" class="game-event">
                            <span class="game-event-date">
                                <small>{{ $event->event_date->format('M') }}</small>
                                <strong>{{ $event->event_date->format('d') }}</strong>
                            </span>
                            <span class="game-event-info">
                                <span class="game-event-title">{{ $event->title }}</span>
                                <span class="game-event-sub">
                                    {{ $event->event_date->format('g:i A') }}
                                    · {{ $event->price > 0 ? '₱' . number_format((float) $event->price) : 'FREE' }}
                                    · {{ $left === 0 ? 'Full' : $left . ($left === 1 ? ' slot left' : ' slots left') }}
                                </span>
                            </span>
                        </a>
                    @empty
                        <p class="reserve-note mb-0">No upcoming events for this game yet.</p>
                    @endforelse

                    <a href="{{ route('events') }}" class="btn btn-info fw-bold btn-sm w-100 mt-3">
                        <i class="bi bi-calendar-event me-2"></i>View all events
                    </a>
                </div>
            </div>
        </div>

        {{-- Products from the same category --}}
        @if ($products->isNotEmpty())
            <div class="d-flex justify-content-between align-items-center mt-5 mb-3">
                <h2 class="checkout-section-title mb-0">Shop {{ $section['title'] }}</h2>
                <a href="{{ route('shop', ['category' => $game->category_id]) }}" class="small fw-bold text-body text-decoration-none">
                    View all <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
                @foreach ($products as $product)
                    @include('partials.product-card', ['product' => $product])
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection