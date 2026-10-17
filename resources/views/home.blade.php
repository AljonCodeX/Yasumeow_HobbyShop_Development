@extends('layouts.app')

@php
    // EDIT THESE: shown in the "Visit us" strip at the bottom
    $shop = [
        'hours'     => 'Add your opening hours here',
        'location'  => 'Add your shop address here',
        'messenger' => '#',
    ];

    $offers = [
        ['icon' => 'bi-bag-fill',       'title' => 'Shop',       'text' => 'Cards, beys, kits, figures and more.', 'url' => '/shop'],
        ['icon' => 'bi-controller',     'title' => 'Play',       'text' => 'Try board games, toy games and PS5.',  'url' => '/games'],
        ['icon' => 'bi-trophy-fill',    'title' => 'Compete',    'text' => 'Join tournaments and races.',          'url' => '/events'],
        ['icon' => 'bi-clock-history',  'title' => 'Rent a PS5', 'text' => '₱50 per hour, up to 2 players.',       'url' => '/games'],
    ];

    // Promo banners: any image in public/images/banners becomes a slide (sorted by file name)
    $banners = collect(glob(public_path('images/banners/*.*')) ?: [])
        ->filter(fn ($path) => preg_match('/\.(jpe?g|png|webp)$/i', $path))
        ->map(fn ($path) => basename($path))
        ->sort()
        ->values();

    $ticker = ['WEEKLY TOURNAMENTS', 'PS5 RENTAL ₱50/HR', 'BEGINNERS WELCOME', 'NEW ARRIVALS'];
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

    {{-- TICKER --}}
    <div class="home-ticker" aria-label="Shop highlights">
        <div class="home-ticker-track">
            @foreach (range(1, 2) as $copy)
                <div class="home-ticker-group" @if ($copy === 2) aria-hidden="true" @endif>
                    @foreach ($ticker as $text)
                        <span>{{ $text }}</span>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
    {{-- WHAT WE OFFER --}}
    <section class="pt-4">
        <div class="row row-cols-2 row-cols-lg-4 g-3">
            @foreach ($offers as $offer)
                <div class="col">
                    <a href="{{ url($offer['url']) }}" class="offer-tile h-100">
                        <i class="bi {{ $offer['icon'] }}"></i>
                        <div>
                            <strong class="d-block">{{ $offer['title'] }}</strong>
                            <span class="small">{{ $offer['text'] }}</span>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </section>

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

        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3 scroll-row">
            @forelse ($events as $event)
                <div class="col">
                    @include('partials.event-card', ['event' => $event])
                </div>
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

            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3 scroll-row">
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
                            <div class="card-footer bg-white border-0 pt-0 pb-3">
                                <span class="game-card-link">View Game Details <i class="bi bi-arrow-right ms-1"></i></span>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- SHOP BY CATEGORY --}}
    @if ($categories->isNotEmpty())
        @php $categoryLimit = 7; @endphp
        <section class="pt-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 fw-bold text-uppercase mb-0">
                    <i class="bi bi-grid me-2"></i>Shop by Category
                </h2>
                <a href="{{ url('/shop') }}" class="small fw-bold text-body text-decoration-none">
                    View All Products <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="category-grid">
                @foreach ($categories as $category)
                    <a href="{{ url('/shop') }}?category={{ $category->id }}"
                       class="category-tile {{ $loop->index >= $categoryLimit ? 'category-extra d-none' : '' }}">
                        <i class="bi {{ $category->icon }}"></i>
                        <span class="fw-bold small">{{ $category->name }}</span>
                    </a>
                @endforeach
            </div>

            @if ($categories->count() > $categoryLimit)
                <div class="text-center mt-3 d-none d-md-block">
                    <button type="button" id="categoryToggle" class="btn btn-outline-dark btn-sm rounded-pill px-4" aria-expanded="false">
                        Show more categories ({{ $categories->count() - $categoryLimit }})
                    </button>
                </div>
            @endif
        </section>
    @endif

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

        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
            @forelse ($products as $product)
                @php $stock = $product->stockStatus(); @endphp
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm p-2">
                        <div class="product-card-img">
                            @if ($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                            @else
                                <i class="bi {{ $product->category?->icon ?? 'bi-box-seam' }}"></i>
                            @endif
                        </div>
                        <div class="card-body px-1 pb-2">
                            <h3 class="h6 fw-bold">{{ $product->name }}</h3>
                            <p class="fw-bold mb-1">₱{{ number_format((float) $product->price) }}</p>
                            @if ($stock === 'in')
                                <small class="text-success fw-bold">In Stock</small>
                            @elseif ($stock === 'low')
                                <small class="text-warning fw-bold">Low Stock ({{ $product->stock_qty }} left)</small>
                            @else
                                <small class="text-danger fw-bold">Sold Out</small>
                            @endif
                        </div>
                        @if ($stock === 'out')
                            <a class="btn btn-secondary btn-sm w-100 disabled">Sold Out</a>
                        @else
                            <a href="{{ url('/shop') }}" class="btn btn-dark btn-sm w-100">View Details</a>
                        @endif
                    </div>
                </div>
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
                    <span>{{ $shop['location'] }}</span>
                </div>
            </div>
            <a href="{{ $shop['messenger'] }}" class="btn btn-info">
                <i class="bi bi-chat-dots me-2"></i>Message us on Messenger
            </a>
        </div>
    </section>
</div>
@endsection