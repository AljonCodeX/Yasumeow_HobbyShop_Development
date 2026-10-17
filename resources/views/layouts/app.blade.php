<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Yasumeow Hobby Shop</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
<nav class="navbar navbar-expand-md site-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            <img src="{{ asset('images/logo.png') }}" alt="Yasumeow" height="40" class="me-2">
            <span class="fw-bold">Yasumeow Hobby Shop</span>
        </a>

        {{-- Phone: search + reservation + menu buttons --}}
        <div class="d-flex align-items-center gap-1 d-md-none">
            <button type="button" class="nav-icon-btn" data-bs-toggle="modal" data-bs-target="#searchModal" aria-label="Search">
                <i class="bi bi-search"></i>
            </button>
            @auth
                <button type="button" class="nav-icon-btn reserve-btn" data-bs-toggle="offcanvas" data-bs-target="#reserveDrawer" aria-controls="reserveDrawer" aria-label="My reservation">
                    <i class="bi bi-bag"></i>
                    @if ($reserveCount > 0)
                        <span class="reserve-badge">{{ $reserveCount }}</span>
                    @endif
                </button>
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

                {{-- Actions: search, reservation, account (right side on desktop) --}}
                <ul class="navbar-nav nav-actions align-items-md-center">
                    {{-- Desktop search --}}
                    <li class="nav-item d-none d-md-block">
                        <button type="button" class="nav-link nav-icon-link" data-bs-toggle="modal" data-bs-target="#searchModal" aria-label="Search">
                            <i class="bi bi-search"></i>
                        </button>
                    </li>

                    {{-- Desktop reservation --}}
                    @auth
                        <li class="nav-item d-none d-md-block">
                            <button type="button" class="nav-link nav-icon-link reserve-btn" data-bs-toggle="offcanvas" data-bs-target="#reserveDrawer" aria-controls="reserveDrawer" aria-label="My reservation">
                                <i class="bi bi-bag"></i>
                                @if ($reserveCount > 0)
                                    <span class="reserve-badge">{{ $reserveCount }}</span>
                                @endif
                            </button>
                        </li>
                    @endauth

                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item ms-md-2 mt-3 mt-md-0 d-grid d-md-block">
                                <a class="btn btn-info btn-sm px-3 rounded-pill fw-bold" href="{{ route('login') }}">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>{{ __('Login') }}
                                </a>
                            </li>
                        @endif
                    @else
                        {{-- Desktop: avatar dropdown --}}
                        <li class="nav-item dropdown d-none d-md-block">
                            <a id="navbarDropdown" class="nav-link nav-avatar" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Account menu">
                                <img src="{{ Auth::user()->avatarUrl() }}" alt="{{ Auth::user()->name }}" class="avatar-circle">
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <h6 class="dropdown-header">{{ Auth::user()->name }}</h6>
                                @if (Auth::user()->is_admin)
                                    <a class="dropdown-item" href="{{ url('/admin') }}">
                                        <i class="bi bi-speedometer2 me-2"></i>Admin Panel
                                    </a>
                                @endif
                                <a class="dropdown-item" href="#">
                                    <i class="bi bi-person me-2"></i>Edit Profile
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="bi bi-gear me-2"></i>Settings
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right me-2"></i>{{ __('Logout') }}
                                </a>
                            </div>
                        </li>

                        {{-- Phone: tap the profile row to show the account links --}}
                        <li class="nav-item d-md-none mt-3 pt-3 border-top border-secondary-subtle">
                            <button type="button" class="account-toggle collapsed" data-bs-toggle="collapse" data-bs-target="#accountMenu" aria-expanded="false" aria-controls="accountMenu">
                                <span class="d-flex align-items-center gap-2">
                                    <img src="{{ Auth::user()->avatarUrl() }}" alt="{{ Auth::user()->name }}" class="avatar-circle">
                                    <strong>{{ Auth::user()->name }}</strong>
                                </span>
                                <i class="bi bi-chevron-down account-chevron"></i>
                            </button>

                            <div class="collapse" id="accountMenu">
                                <ul class="navbar-nav">
                                    @if (Auth::user()->is_admin)
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ url('/admin') }}"><i class="bi bi-speedometer2 me-2"></i>Admin Panel</a>
                                        </li>
                                    @endif
                                    <li class="nav-item">
                                        <a class="nav-link" href="#"><i class="bi bi-person me-2"></i>Edit Profile</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#"><i class="bi bi-gear me-2"></i>Settings</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="bi bi-box-arrow-right me-2"></i>{{ __('Logout') }}
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

        <footer class="site-footer bg-white border-top mt-5 py-5">
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
                            <a href="#" class="btn btn-outline-dark rounded-3 flex-fill">
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
                    <form action="{{ url('/shop') }}" method="GET" role="search">
                        <div class="input-group search-box">
                            <span class="input-group-text border-0 bg-transparent">
                                <i class="bi bi-search fs-4"></i>
                            </span>
                            <input id="searchInput" type="search" name="q" class="form-control border-0 fs-4" placeholder="Search" autocomplete="off">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Reservation drawer -->
    @auth
        <div class="offcanvas offcanvas-end site-offcanvas reserve-drawer" tabindex="-1" id="reserveDrawer"
             aria-labelledby="reserveDrawerLabel" @if (session('open_reserve')) data-auto-open @endif>
            <div class="offcanvas-header">
                <span class="fw-bold" id="reserveDrawerLabel">My Reservation ({{ $reserveCount }})</span>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>

            <div class="offcanvas-body d-flex flex-column">
                @if ($reserveItems->isEmpty())
                    <div class="reserve-empty">
                        <i class="bi bi-bag"></i>
                        <p class="fw-bold mb-1">Your reservation is empty</p>
                        <p class="small mb-3">Pick something from the shop and it will show up here.</p>
                        <a href="{{ url('/shop') }}" class="shop-item-btn">Browse shop</a>
                    </div>
                @else
                    @if (session('open_reserve') && session('error'))
                        <p class="reserve-error">{{ session('error') }}</p>
                    @endif

                    <ul class="reserve-list">
                        @foreach ($reserveItems as $item)
                            @php $p = $item['product']; @endphp
                            <li class="reserve-row">
                                <div class="reserve-thumb">
                                    @if ($p->image)
                                        <img src="{{ asset('storage/' . $p->image) }}" alt="{{ $p->name }}">
                                    @else
                                        <i class="bi {{ $p->category?->icon ?? 'bi-box-seam' }}"></i>
                                    @endif
                                </div>

                                <div class="reserve-info">
                                    <a href="{{ route('products.show', $p) }}" class="reserve-name">{{ $p->name }}</a>
                                    <span class="reserve-price">₱{{ number_format($item['subtotal']) }}</span>

                                    <div class="reserve-qty">
                                        <form method="POST" action="{{ route('reserve.decrease', $p) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="reserve-qty-btn" aria-label="Decrease quantity"><i class="bi bi-dash"></i></button>
                                        </form>
                                        <span>{{ $item['qty'] }}</span>
                                        <form method="POST" action="{{ route('reserve.increase', $p) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="reserve-qty-btn" aria-label="Increase quantity"><i class="bi bi-plus"></i></button>
                                        </form>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('reserve.remove', $p) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="reserve-remove" aria-label="Remove {{ $p->name }}"><i class="bi bi-trash"></i></button>
                                </form>
                            </li>
                        @endforeach
                    </ul>

                    <div class="reserve-foot mt-auto">
                        <div class="reserve-total">
                            <span>Total</span>
                            <span>₱{{ number_format($reserveTotal) }}</span>
                        </div>
                        <p class="reserve-note">Pick up at the shop. Pay in store or GCash.</p>
                        <form method="POST" action="{{ route('reserve.confirm') }}">
                            @csrf
                            <button type="submit" class="shop-item-btn">Confirm reservation</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    @endauth

    <script>
        // Put the cursor in the search box when the search page opens
        document.getElementById('searchModal').addEventListener('shown.bs.modal', function () {
            document.getElementById('searchInput').focus();
        });
    </script>
</body>
</html>