@extends('layouts.app')

@php
    $shop   = config('shop');
    $offers = $shop['offers'];
    // Promo banners: any image in public/images/banners becomes a slide (sorted by file name)
    $banners = collect(glob(public_path('images/banners/*.*')) ?: [])
        ->filter(fn ($path) => preg_match('/\.(jpe?g|png|webp)$/i', $path))
        ->map(fn ($path) => basename($path))
        ->sort()
        ->values();
@endphp

@section('content')
<div class="container">

    {{-- HERO CAROUSEL --}}
    <div id="homeCarousel" class="carousel slide home-hero"
         @if ($banners->isNotEmpty()) data-bs-ride="carousel" data-bs-interval="5000" @endif>

        @if ($banners->isNotEmpty())
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                @foreach ($banners as $banner)
                    <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="{{ $loop->iteration }}" aria-label="Slide {{ $loop->iteration + 1 }}"></button>
                @endforeach
            </div>
        @endif

        <div class="carousel-inner">
            <div class="carousel-item active">
                <section class="home-slide home-slide-hero text-white"
                         style="--banner-image: url('{{ asset('images/banner_home.png') }}');">
                    <div class="home-slide-text">
                        <p class="small text-uppercase mb-2 d-none d-md-block">Games &bull; Collectibles &bull; Hobby Items &bull; Events</p>
                        <h1 class="fw-bold text-uppercase">
                            Your Hobby.<br>
                            <span class="text-info">Your Community.</span>
                        </h1>
                        <p class="mb-3 d-none d-md-block">Discover hobby items, join events, and more.</p>
                        <div class="home-slide-actions d-flex flex-wrap gap-2">
                            <a href="{{ url('/shop') }}" class="btn btn-info">
                                <i class="bi bi-cart me-2"></i>Explore Products
                            </a>
                            <a href="{{ url('/events') }}" class="btn btn-outline-light">
                                <i class="bi bi-calendar-event me-2"></i>View Events
                            </a>
                            <a href="{{ url('/games') }}" class="btn btn-outline-light d-none d-md-inline-block">
                                <i class="bi bi-controller me-2"></i>Browse Games
                            </a>
                        </div>
                    </div>
                </section>
            </div>

            @foreach ($banners as $banner)
                <div class="carousel-item">
                    <div class="home-slide home-slide-promo">
                        <img src="{{ asset('images/banners/' . $banner) }}" alt="Yasumeow Hobby Shop promo" loading="lazy">
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- FEATURED PRODUCTS --}}
    <section class="pt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 fw-bold text-uppercase mb-0">
                <i class="bi bi-star me-2"></i>Featured Products
            </h2>
            <a href="{{ url('/shop') }}" class="small fw-bold text-body text-decoration-none">
                View All Products <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3" data-load-more data-step="4" data-step-mobile="2">
            @forelse ($products as $product)
                @include('partials.product-card', ['product' => $product])
            @empty
                <div class="col-12">
                    <div class="home-empty">
                        <i class="bi bi-box-seam"></i>
                        <p class="mb-0">No products yet. Check back soon!</p>
                    </div>
                </div>
            @endforelse
        </div>
    </section>

    {{-- SHOP BY CATEGORY --}}
    @if ($categories->isNotEmpty())
        @php $categoryLimit = 7; @endphp
        <section class="pt-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 fw-bold text-uppercase mb-0">
                    <i class="bi bi-grid me-2"></i>Browse by Category
                </h2>
                <a href="{{ url('/shop') }}" class="small fw-bold text-body text-decoration-none">
                    View All Products <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="category-grid">
                @foreach ($categories as $category)
                    <a href="{{ url('/shop') }}?category={{ $category->id }}"
                       class="category-tile {{ $loop->index >= $categoryLimit ? 'category-extra d-none' : '' }}">
                        @if ($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="category-tile-img" loading="lazy">
                        @else
                            <i class="bi {{ $category->icon }}"></i>
                        @endif
                        <span class="fw-bold small">{{ $category->name }}</span>
                    </a>
                @endforeach
            </div>

            @if ($categories->count() > $categoryLimit)
                <div class="text-center mt-3 d-none d-md-block">
                    <button type="button" id="categoryToggle" class="btn btn-link load-more-link p-0 fw-bold text-body text-decoration-none" aria-expanded="false">
                        Show more categories ({{ $categories->count() - $categoryLimit }})
                    </button>
                </div>
            @endif
        </section>
    @endif

    {{-- UPCOMING EVENTS --}}
    <section class="pt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 fw-bold text-uppercase mb-0">
                <i class="bi bi-calendar-event me-2"></i>Upcoming Events
            </h2>
            <a href="{{ url('/events') }}" class="small fw-bold text-body text-decoration-none">
                View All Events <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3" data-load-more data-step="4" data-step-mobile="2">
            @forelse ($events as $event)
                @include('partials.event-item', ['event' => $event])
            @empty
                <div class="col-12">
                    <div class="home-empty">
                        <i class="bi bi-calendar-x"></i>
                        <p class="mb-0">No upcoming events yet. Check back soon!</p>
                    </div>
                </div>
            @endforelse
        </div>
    </section>

    {{-- TOP PLAYERS --}}
    @if ($topPlayers->isNotEmpty())
        @php $rest = $topPlayers->slice(3); @endphp
        <section class="pt-5">
            <h2 class="h5 fw-bold text-uppercase mb-4">
                <i class="bi bi-trophy me-2"></i>Top Players
            </h2>

            <div class="row g-4 align-items-end justify-content-center">
                {{-- 1st to 3rd --}}
                <div class="col-lg-7">
                    <div class="podium">
                        @foreach ($topPlayers->take(3) as $row)
                            @php $place = ['1st', '2nd', '3rd'][$loop->index]; @endphp
                            <div class="podium-slot podium-{{ $loop->iteration }}">
                                <i class="bi bi-award-fill podium-medal" aria-hidden="true"></i>
                                @include('partials.player-avatar', ['user' => $row->user, 'class' => 'podium-avatar'])
                                <strong class="podium-name">{{ $row->user->name }}</strong>
                                <span class="podium-meta">
                                    {{ (int) $row->wins }} {{ (int) $row->wins === 1 ? 'win' : 'wins' }}
                                    in {{ $row->events_played }} events
                                </span>
                                <div class="podium-step">
                                    <span class="podium-place">{{ $place }}</span>
                                    <span class="podium-points">{{ number_format($row->total_points) }} <small>pts</small></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- 4th to 10th --}}
                @if ($rest->isNotEmpty())
                    <div class="col-lg-5">
                        <ol class="rank-list">
                            @foreach ($rest as $row)
                                <li class="rank-row">
                                    <span class="rank-num">{{ $loop->iteration + 3 }}th</span>
                                    @include('partials.player-avatar', ['user' => $row->user, 'class' => 'rank-avatar'])
                                    <div class="rank-info">
                                        <strong>{{ $row->user->name }}</strong>
                                        <span>
                                            {{ (int) $row->wins }} {{ (int) $row->wins === 1 ? 'win' : 'wins' }}
                                            in {{ $row->events_played }} events
                                        </span>
                                    </div>
                                    <span class="rank-points">{{ number_format($row->total_points) }} <small>pts</small></span>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- GAMES --}}
    @if ($games->isNotEmpty())
        <section class="pt-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 fw-bold text-uppercase mb-0">
                    <i class="bi bi-controller me-2"></i>Games You Can Play
                </h2>
                <a href="{{ url('/games') }}" class="small fw-bold text-body text-decoration-none">
                    View All Games <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            {{-- PS5 rental info --}}
            <div class="games-note d-flex align-items-center gap-3 mb-3">
                <i class="bi bi-clock-history"></i>
                <div>
                    <strong>PS5 Rental</strong>
                    <span class="d-block small">₱50 per hour for up to 2 players. Pick a game and play on the shop PS5.</span>
                </div>
            </div>

            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3" data-load-more data-step="4" data-step-mobile="2">
                @foreach ($games as $game)
                    @include('partials.game-item', ['game' => $game, 'sectionSlug' => ''])
                @endforeach
            </div>
        </section>
    @endif

    {{-- VISIT US --}}
    <section class="pt-5 pb-4">
        <div class="shop-info">
            <div class="shop-info-item">
                <i class="bi bi-clock"></i>
                <div>
                    <strong class="d-block">Opening hours</strong>
                    <span>{{ $shop['hours'] }}</span>
                </div>
            </div>
            <div class="shop-info-item">
                <i class="bi bi-geo-alt"></i>
                <div>
                    <strong class="d-block">Find us</strong>
                    <a href="{{ $shop['map'] }}" target="_blank" rel="noopener" class="text-white">{{ $shop['location'] }}</a>
                </div>
            </div>
            <a href="{{ $shop['messenger'] }}" target="_blank" rel="noopener" class="btn btn-info">
                <i class="bi bi-chat-dots me-2"></i>Message us on Messenger
            </a>
        </div>
    </section>
</div>
@include('partials.rate-modal')
@endsection