@extends('layouts.app')

@section('content')
<div class="shop-page">
    <div class="container">

        <header class="shop-head">
            <div>
                <h1 class="shop-title">Search</h1>
                <p class="shop-sub">
                    @if ($q === '')
                        Type something in the search window to begin.
                    @else
                        {{ $total }} {{ $total === 1 ? 'result' : 'results' }} for "{{ $q }}"
                    @endif
                </p>
            </div>
        </header>

        @if ($q !== '' && $total === 0)
            <div class="shop-empty">
                <i class="bi bi-search"></i>
                <p class="mb-0">Nothing found. Try a shorter word, or browse the <a href="{{ route('shop') }}">shop</a>.</p>
            </div>
        @endif

        @if ($products->isNotEmpty())
            <h2 class="checkout-section-title mt-4">Products ({{ $products->count() }})</h2>
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
                @foreach ($products as $product)
                    @include('partials.product-card', ['product' => $product])
                @endforeach
            </div>
        @endif

        @if ($games->isNotEmpty())
            <h2 class="checkout-section-title mt-4">Games ({{ $games->count() }})</h2>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3">
                @foreach ($games as $game)
                    <div class="col">
                        <a href="{{ url('/games/' . $game->slug) }}" class="game-card card h-100 border-0 shadow-sm overflow-hidden text-decoration-none">
                            <div class="game-card-cover">
                                <span class="game-card-badge">{{ $game->type }}</span>
                                <i class="bi {{ $game->icon }}"></i>
                            </div>
                            <div class="card-body">
                                <h3 class="h6 fw-bold text-body mb-2">{{ $game->name }}</h3>
                                <p class="small text-muted mb-0 game-card-desc">{{ $game->description }}</p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

        @if ($events->isNotEmpty())
            <h2 class="checkout-section-title mt-4">Events ({{ $events->count() }})</h2>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3">
                @foreach ($events as $event)
                    <div class="col">
                        @include('partials.event-card', ['event' => $event])
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection