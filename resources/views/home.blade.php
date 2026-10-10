@extends('layouts.app')

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
</div>
@endsection