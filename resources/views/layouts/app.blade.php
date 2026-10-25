<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Yasumeow Hobby Shop</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Light / dark mode: runs before the page is drawn so it doesn't flash the wrong colors -->
    <script>
        (function () {
            var saved = null;
            try { saved = localStorage.getItem('ym-theme'); } catch (e) {}
            var dark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.setAttribute('data-bs-theme', saved || (dark ? 'dark' : 'light'));
        })();
    </script>

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
<nav class="navbar navbar-expand-md site-navbar sticky-top" data-bs-theme="dark">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            <img src="{{ asset('images/logo.png') }}" alt="Yasumeow" height="40" class="me-2">
            <span class="fw-bold">Yasumeow Hobby Shop</span>
        </a>

        {{-- Phone: search + bag + menu buttons --}}
        <div class="d-flex align-items-center gap-1 d-md-none">
            <button type="button" class="nav-icon-btn" data-bs-toggle="modal" data-bs-target="#searchModal" aria-label="Search">
                <i class="bi bi-search"></i>
            </button>
            @auth
            <a href="{{ route('bag') }}" class="nav-icon-btn reserve-btn" aria-label="My bag">
                <i class="bi bi-bag"></i>
                @if ($reserveCount > 0)
                    <span class="reserve-badge">{{ $reserveCount }}</span>
                @endif
            </a>
            @endauth
            <button type="button" class="nav-icon-btn" data-bs-toggle="offcanvas" data-bs-target="#mainMenu" aria-controls="mainMenu" aria-label="Open menu">
                <i class="bi bi-list"></i>
            </button>
        </div>

        <div class="offcanvas offcanvas-end site-offcanvas" tabindex="-1" id="mainMenu" aria-labelledby="mainMenuLabel">
            <div class="offcanvas-header">
                <span class="fw-bold" id="mainMenuLabel">Menu</span>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>

            <div class="offcanvas-body">
                @php
                    $navLinks = [
                        ['label' => 'Home',   'url' => '/',       'icon' => 'bi-house',          'active' => request()->is('/')],
                        ['label' => 'Games',  'url' => '/games',  'icon' => 'bi-controller',     'active' => request()->is('games*')],
                        ['label' => 'Shop',   'url' => '/shop',   'icon' => 'bi-bag',            'active' => request()->is('shop*')],
                        ['label' => 'Events', 'url' => '/events', 'icon' => 'bi-calendar-event', 'active' => request()->is('events*')],
                        ['label' => 'About',  'url' => '/about',  'icon' => 'bi-info-circle',    'active' => request()->is('about*')],
                    ];
                @endphp

                {{-- Page links (centered on desktop) --}}
                <ul class="navbar-nav nav-links align-items-md-center">
                    @foreach ($navLinks as $link)
                        <li class="nav-item">
                            <a class="nav-link {{ $link['active'] ? 'active' : '' }}"
                               href="{{ url($link['url']) }}"
                               @if ($link['active']) aria-current="page" @endif>
                                <i class="bi {{ $link['icon'] }} d-md-none me-2"></i>{{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                {{-- Actions: search, bag, account (right side on desktop) --}}
                <ul class="navbar-nav nav-actions align-items-md-center">
                    {{-- Desktop search --}}
                    <li class="nav-item d-none d-md-block">
                        <button type="button" class="nav-link nav-icon-link" data-bs-toggle="modal" data-bs-target="#searchModal" aria-label="Search">
                            <i class="bi bi-search"></i>
                        </button>
                    </li>


                    {{-- Desktop bag --}}
                    @auth
                        <li class="nav-item d-none d-md-block">
                        <a href="{{ route('bag') }}" class="nav-link nav-icon-link reserve-btn" aria-label="My bag">
                            <i class="bi bi-bag"></i>
                            @if ($reserveCount > 0)
                                <span class="reserve-badge">{{ $reserveCount }}</span>
                            @endif
                        </a>
                        </li>
                    @endauth

                    @guest
                        @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Log in</a>
                        </li>
                        @endif
                    @else
                        {{-- Desktop: avatar dropdown --}}
                        <li class="nav-item d-none d-md-block">
                            <a class="nav-link nav-avatar" href="{{ route('account.dashboard') }}" aria-label="My account">
                                @include('partials.avatar')
                            </a>
                        </li>

                        {{-- Phone: tap the profile row to show the account links --}}
                        <li class="nav-item d-md-none mt-3 pt-3 border-top border-secondary-subtle">
                            <button type="button" class="account-toggle collapsed"
                                    data-bs-toggle="collapse" data-bs-target="#accountLinks"
                                    aria-expanded="false" aria-controls="accountLinks">
                                <span class="d-flex align-items-center gap-2">
                                    @include('partials.avatar')
                                    <strong>{{ Auth::user()->name }}</strong>
                                </span>
                                <i class="bi bi-chevron-down account-chevron"></i>
                            </button>

                            <div class="collapse" id="accountLinks">
                                <ul class="navbar-nav">
                                    @php
                                        $accountLinks = [
                                            ['route' => 'account.dashboard',    'icon' => 'bi-grid',      'label' => 'Dashboard'],
                                            ['route' => 'account.info',         'icon' => 'bi-person',    'label' => 'Account Info'],
                                            ['route' => 'account.favourites',   'icon' => 'bi-heart',     'label' => 'Favourites'],
                                            ['route' => 'account.reservations', 'icon' => 'bi-bag-check', 'label' => 'Reservations'],
                                            ['route' => 'account.events',       'icon' => 'bi-calendar-event', 'label' => 'My Events'],
                                            ['route' => 'account.appearance',   'icon' => 'bi-palette',   'label' => 'Appearance'],
                                        ];
                                    @endphp

                                    @foreach ($accountLinks as $link)
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs($link['route']) ? 'active' : '' }}"
                                            href="{{ route($link['route']) }}">
                                            <i class="bi {{ $link['icon'] }} me-2"></i>{{ $link['label'] }}
                                                @if ($link['route'] === 'account.favourites' && $favouriteMenuCount > 0)
                                                    <span class="menu-badge">{{ $favouriteMenuCount }}</span>
                                                @endif
                                                @if ($link['route'] === 'account.reservations' && $reservationMenuCount > 0)
                                                    <span class="menu-badge">{{ $reservationMenuCount }}</span>
                                                @endif
                                                @if ($link['route'] === 'account.events' && $eventMenuCount > 0)
                                                    <span class="menu-badge">{{ $eventMenuCount }}</span>
                                                @endif
                                            </a>
                                        </li>
                                    @endforeach

                                    @if (Auth::user()->is_admin)
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ url('/admin') }}">
                                                <i class="bi bi-speedometer2 me-2"></i>Admin Panel
                                            </a>
                                        </li>
                                    @endif

                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="bi bi-box-arrow-right me-2"></i>Log out
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </div>
</nav>

@auth
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
@endauth

        <main class="py-4">
            @if (session('success') || session('error'))
                <div class="container">
                    <div class="alert alert-{{ session('success') ? 'success' : 'danger' }} alert-dismissible fade show" role="alert">
                        {{ session('success') ?? session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="site-footer border-top mt-5 py-5">
            <div class="container">
                <div class="row gy-4">
                    <div class="col-md-7">
                        <img src="{{ asset('images/footer_logo.png') }}" alt="Yasumeow" class="footer-logo">
                        <p class="mb-0">
                            Your local hub for hobbies, collectibles, and gaming events. Join the YasuMeow
                            community and share your passion for play!
                        </p>
                    </div>

                    <div class="col-md-5">
                        <h5>Follow Us</h5>
                        <div class="footer-social">
                            <a href="#" class="text-body" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="text-body" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        </div>

                        <h5 class="mt-4">Contact Us</h5>
                        <div class="footer-contact">
                            <a href="{{ config('shop.messenger') }}" target="_blank" rel="noopener" class="btn btn-outline-dark rounded-3 flex-fill">
                                <i class="bi bi-chat-dots me-1"></i> Messenger
                            </a>
                            <a href="#" class="btn btn-outline-dark rounded-3 flex-fill">
                                <i class="bi bi-question-circle me-1"></i> FAQ
                            </a>
                        </div>
                    </div>
                </div>

                <div class="footer-bottom">
                    <div class="footer-links small">
                        <a href="{{ route('about') }}" class="text-body text-decoration-none">About Us</a>
                        <span class="footer-sep">|</span>
                        <a href="#" class="text-body text-decoration-none">Terms and Conditions</a>
                        <span class="footer-sep">|</span>
                        <a href="#" class="text-body text-decoration-none">Privacy Policy</a>
                    </div>
                    <small class="text-muted">&copy; 2026 Yasumeow Hobby Shop</small>
                </div>
            </div>
        </footer>
    </div>

    <!-- Full-screen search -->
    <div class="modal fade" id="searchModal" tabindex="-1" aria-label="Search" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content search-modal">
                <div class="d-flex justify-content-end p-4">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                   <div class="container search-modal-body">
                    <form action="{{ route('search') }}" method="GET" role="search">
                        <div class="input-group search-box">
                            <span class="input-group-text border-0 bg-transparent">
                                <i class="bi bi-search fs-4"></i>
                            </span>
                            <input id="searchInput" type="search" name="q" class="form-control border-0 fs-4" placeholder="Search" autocomplete="off">
                        </div>
                    </form>

                    {{-- Product suggestions appear here while typing --}}
                    <div id="searchSuggestions" class="search-suggestions"
                        data-url="{{ route('search.suggestions') }}"
                        data-results="{{ route('search') }}"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Put the cursor in the search box when the search page opens
        document.getElementById('searchModal').addEventListener('shown.bs.modal', function () {
            document.getElementById('searchInput').focus();
        });
    </script>
</body>
</html>