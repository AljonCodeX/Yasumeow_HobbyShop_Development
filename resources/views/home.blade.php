@extends('layouts.app')

@php
    $events = [
        ['month' => 'OCT', 'day' => '17', 'title' => 'Beyblade Tournament',    'time' => '2:00 PM',  'price' => 100, 'icon' => 'bi-lightning-charge'],
        ['month' => 'OCT', 'day' => '24', 'title' => 'Tamiya Mini 4WD Race',   'time' => '10:00 AM', 'price' => 0,   'icon' => 'bi-speedometer2'],
        ['month' => 'OCT', 'day' => '31', 'title' => 'Pokemon TCG Casual Play', 'time' => '1:00 PM',  'price' => 0,   'icon' => 'bi-stars'],
        ['month' => 'NOV', 'day' => '07', 'title' => 'PS5 Game Night',         'time' => '5:00 PM',  'price' => 150, 'icon' => 'bi-controller'],
    ];

    $categories = [
        ['name' => 'Card Games',             'icon' => 'bi-suit-spade-fill'],
        ['name' => 'Beyblade',               'icon' => 'bi-tornado'],
        ['name' => 'Tamiya',                 'icon' => 'bi-car-front-fill'],
        ['name' => 'PS5',                    'icon' => 'bi-controller'],
        ['name' => 'Figures & Collectibles', 'icon' => 'bi-trophy'],
        ['name' => 'Accessories',            'icon' => 'bi-headset'],
        ['name' => 'Others',                 'icon' => 'bi-three-dots'],
    ];

    // stock: 'in', 'low', or 'out'
    $products = [
        ['name' => 'Pokemon TCG Booster Box',  'price' => 2500, 'stock' => 'in',  'icon' => 'bi-suit-spade-fill'],
        ['name' => 'Beyblade X Starter Set',   'price' => 850,  'stock' => 'low', 'icon' => 'bi-tornado'],
        ['name' => 'Tamiya Mini 4WD',          'price' => 1500, 'stock' => 'in',  'icon' => 'bi-car-front-fill'],
        ['name' => 'PS5 Game - Spider-Man 2',  'price' => 2495, 'stock' => 'out', 'icon' => 'bi-controller'],
        ['name' => 'Naruto Figure',            'price' => 1200, 'stock' => 'in',  'icon' => 'bi-trophy'],
    ];
@endphp

@section('content')
<div class="container">
    <section class="hero-banner text-white d-flex align-items-center rounded-4 overflow-hidden"
        style="--banner-image: url('{{ asset('images/banner_home.png') }}');">
        <div>
            <p class="small text-uppercase mb-2">Games &bull; Collectibles &bull; Hobby Items &bull; Events</p>
            <h1 class="display-6 fw-bold text-uppercase">
                Your Hobby.<br>
                <span class="text-info">Your Community.</span>
            </h1>
            <p class="mb-4">Discover hobby items, join events, and more.</p>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ url('/shop') }}" class="btn btn-info">
                    <i class="bi bi-cart me-2"></i>Explore Products
                </a>
                <a href="{{ url('/events') }}" class="btn btn-outline-light">
                    <i class="bi bi-calendar-event me-2"></i>View Events
                </a>
            </div>
        </div>
    </section>

    <section class="pt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 fw-bold text-uppercase mb-0">
                <i class="bi bi-calendar-event me-2"></i>Upcoming Events
            </h2>
            <a href="{{ url('/events') }}" class="small fw-bold text-body text-decoration-none">
                View All Events <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3">
            @foreach ($events as $event)
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm overflow-hidden">
                        <div class="event-card-img position-relative">
                            <i class="bi {{ $event['icon'] }}"></i>
                            <div class="event-date">
                                <small class="d-block fw-bold">{{ $event['month'] }}</small>
                                <span class="fs-5 fw-bold">{{ $event['day'] }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <h3 class="h6 fw-bold">{{ $event['title'] }}</h3>
                            <p class="small text-muted mb-1">
                                <i class="bi bi-geo-alt me-1"></i>Yasumeow Hobby Shop
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted">
                                    <i class="bi bi-clock me-1"></i>{{ $event['time'] }}
                                </span>
                                @if ($event['price'] > 0)
                                    <span class="badge bg-danger">₱{{ $event['price'] }}</span>
                                @else
                                    <span class="badge bg-success">FREE</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 pt-0 pb-3">
                            <a href="{{ url('/events') }}" class="btn btn-dark btn-sm w-100">View Event</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="pt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 fw-bold text-uppercase mb-0">
                <i class="bi bi-grid me-2"></i>Shop by Category
            </h2>
            <a href="{{ url('/shop') }}" class="small fw-bold text-body text-decoration-none">
                View All Categories <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="category-grid">
            @foreach ($categories as $category)
                <a href="{{ url('/shop') }}" class="category-tile">
                    <i class="bi {{ $category['icon'] }}"></i>
                    <span class="fw-bold small">{{ $category['name'] }}</span>
                </a>
            @endforeach
        </div>
    </section>

    <section class="pt-5 pb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 fw-bold text-uppercase mb-0">
                <i class="bi bi-star me-2"></i>Featured Products
            </h2>
            <a href="{{ url('/shop') }}" class="small fw-bold text-body text-decoration-none">
                View All Products <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-3">
            @foreach ($products as $product)
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm p-2">
                        <div class="product-card-img">
                            <i class="bi {{ $product['icon'] }}"></i>
                        </div>
                        <div class="card-body px-1 pb-2">
                            <h3 class="h6 fw-bold">{{ $product['name'] }}</h3>
                            <p class="fw-bold mb-1">₱{{ number_format($product['price']) }}</p>
                            @if ($product['stock'] === 'in')
                                <small class="text-success fw-bold">In Stock</small>
                            @elseif ($product['stock'] === 'low')
                                <small class="text-warning fw-bold">Low Stock</small>
                            @else
                                <small class="text-danger fw-bold">Sold Out</small>
                            @endif
                        </div>
                        @if ($product['stock'] === 'out')
                            <a class="btn btn-secondary btn-sm w-100 disabled">Sold Out</a>
                        @else
                            <a href="{{ url('/shop') }}" class="btn btn-dark btn-sm w-100">View Details</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection