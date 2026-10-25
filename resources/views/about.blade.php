@extends('layouts.app')

@php $shop = config('shop'); @endphp

@section('content')
<div class="shop-page">
    <div class="container" style="max-width: 900px;">

        {{-- Banner slideshow: slide 1 is the building, the rest are community photos --}}
        @php
            $hasPhoto = ! empty($shop['photo']) && file_exists(public_path($shop['photo']));
            $gallery  = collect($shop['gallery'] ?? [])->filter(fn ($p) => file_exists(public_path($p)))->values();
            $total    = 1 + $gallery->count();
        @endphp

        <section class="about-banner">
            <div class="about-banner-text">
                <h1 class="shop-title">About Us</h1>
                <p class="about-tagline">Your local hub for hobbies, collectibles and gaming events.</p>
            </div>

            <div id="aboutCarousel" class="carousel slide carousel-fade about-carousel"
                 @if ($total > 1) data-bs-ride="carousel" data-bs-interval="5000" @endif>

                @if ($total > 1)
                    <div class="carousel-indicators">
                        @for ($i = 0; $i < $total; $i++)
                            <button type="button" data-bs-target="#aboutCarousel" data-bs-slide-to="{{ $i }}"
                                    class="{{ $i === 0 ? 'active' : '' }}" @if ($i === 0) aria-current="true" @endif
                                    aria-label="Slide {{ $i + 1 }}"></button>
                        @endfor
                    </div>
                @endif

                <div class="carousel-inner">
                    <div class="carousel-item active">
                        @if ($hasPhoto)
                            <img src="{{ asset($shop['photo']) }}" alt="Yasumeow Hobby Shop building"
                                 class="about-slide-img about-slide-building" fetchpriority="high">
                        @endif
                    </div>

                    @foreach ($gallery as $photo)
                        <div class="carousel-item">
                            <img src="{{ asset($photo) }}" alt="Yasumeow Hobby Shop community"
                                 class="about-slide-img about-slide-photo" loading="lazy">
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Our story --}}
        <section class="about-section">
            <h2 class="checkout-section-title">Our story</h2>
            <p>Yasumeow Hobby Shop was built from a love of hobbies, games, and collecting. Owned by Yasumeow, the shop is located in Pulong Tamo, San Ildefonso, Bulacan, and welcomes hobby enthusiasts looking for a place to discover new products and enjoy their favorite games.</p>
            <p>More than just a shop, Yasumeow aims to bring people together through shared interests, friendly competition, and a growing local hobby community. Whether you're a collector, a casual player, or someone just starting out, you're always welcome here.</p>
        </section>

        {{-- What we do --}}
        @php
            $aboutTexts = [
                'Shop'    => 'Discover trading cards, Beyblades, model kits, figures, and other hobby essentials.',
                'Play'    => 'Enjoy your favorite games, practice your skills, and connect with fellow hobby enthusiasts.',
                'Compete' => 'Join weekly tournaments, challenge other players, and enjoy friendly competition with the local community.',
            ];
        @endphp
        <section class="about-section">
            <h2 class="checkout-section-title">What we do</h2>
            <div class="row row-cols-1 row-cols-md-3 g-3">
                @foreach ($shop['offers'] as $offer)
                    <div class="col">
                        <a href="{{ url($offer['url']) }}" class="offer-tile h-100">
                            <i class="bi {{ $offer['icon'] }}"></i>
                            <div>
                                <strong class="d-block">{{ $offer['title'] }}</strong>
                                <span class="small">{{ $aboutTexts[$offer['title']] ?? $offer['text'] }}</span>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Community --}}
        <section class="about-section">
            <h2 class="checkout-section-title">Yasumeow Community League</h2>
            <p>The Yasumeow Community League is our weekly tournament where hobby enthusiasts from nearby areas come together to play, compete, and have fun. It's a chance to meet fellow players, test your skills, and build friendships through friendly competition. Whether you're a beginner or an experienced player, everyone is welcome to join the community!</p>

            <a href="{{ route('events') }}" class="fw-bold text-body d-inline-block mt-2">Join This Week's Tournament <i class="bi bi-arrow-right"></i></a>
        </section>

        {{-- Map --}}
        @if (! empty($shop['embed']))
            <section class="about-section">
                <h2 class="checkout-section-title">Find us</h2>

                <div class="about-place">
                    <iframe src="{{ $shop['embed'] }}" title="Yasumeow Hobby Shop on Google Maps"
                            loading="lazy" allowfullscreen
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>

                <a href="{{ $shop['map'] }}" target="_blank" rel="noopener" class="fw-bold text-body">
                    Open in Google Maps <i class="bi bi-arrow-up-right"></i>
                </a>
            </section>
        @endif

        {{-- Visit us --}}
        <section class="about-section">
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
</div>
@endsection