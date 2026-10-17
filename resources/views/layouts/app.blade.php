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

        {{-- Phone: search + menu buttons --}}
        <div class="d-flex align-items-center gap-1 d-md-none">
            <button type="button" class="nav-icon-btn" data-bs-toggle="modal" data-bs-target="#searchModal" aria-label="Search">
                <i class="bi bi-search"></i>
            </button>
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

                <ul class="navbar-nav ms-auto align-items-md-center">
                    @foreach ($navLinks as $link)
                        <li class="nav-item">
                            <a class="nav-link {{ $link['active'] ? 'active' : '' }}"
                               href="{{ url($link['url']) }}"
                               @if ($link['active']) aria-current="page" @endif>
                                <i class="bi {{ $link['icon'] }} d-md-none me-2"></i>{{ $link['label'] }}
                            </a>
                        </li>
                    @endforeach

                    {{-- Desktop search --}}
                    <li class="nav-item d-none d-md-block">
                        <button type="button" class="nav-link nav-icon-link" data-bs-toggle="modal" data-bs-target="#searchModal" aria-label="Search">
                            <i class="bi bi-search"></i>
                        </button>
                    </li>

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

                        {{-- Phone: account links shown directly --}}
                        <li class="nav-item d-md-none mt-3 pt-3 border-top border-secondary-subtle">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <img src="{{ Auth::user()->avatarUrl() }}" alt="{{ Auth::user()->name }}" class="avatar-circle">
                                <strong>{{ Auth::user()->name }}</strong>
                            </div>
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

        <footer class="bg-white border-top mt-5 py-5">
            <div class="container">
                <div class="row">
                    <div class="col-md-7 mb-4 mb-md-0">
                        <img src="{{ asset('images/footer_logo.png') }}" alt="Yasumeow" height="64" class="mb-3">
                        <p class="mb-3">
                            Your local hub for hobbies, collectibles, and gaming events. Join the YasuMeow
                            community and share your passion for play!
                        </p>
                        <p class="small mb-1">
                            <a href="#" class="text-body text-decoration-none">Terms and Conditions</a>
                            |
                            <a href="#" class="text-body text-decoration-none">Privacy Policy</a>
                        </p>
                        <small class="text-muted">&copy; 2026 Yasumeow Hobby Shop</small>
                    </div>

                    <div class="col-md-5">
                        <h5>Follow Us</h5>
                        <a href="#" class="text-body fs-2 me-3" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-body fs-2 me-3" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-body fs-2" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>

                        <h5 class="mt-4">Contact Us</h5>
                        <div class="d-flex gap-3">
                            <a href="#" class="btn btn-outline-dark rounded-3 flex-fill">
                                <i class="bi bi-chat-dots me-1"></i> Messenger
                            </a>
                            <a href="#" class="btn btn-outline-dark rounded-3 flex-fill">
                                <i class="bi bi-question-circle me-1"></i> FAQ
                            </a>
                        </div>
                    </div>
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

    <script>
        // Put the cursor in the search box when the search page opens
        document.getElementById('searchModal').addEventListener('shown.bs.modal', function () {
            document.getElementById('searchInput').focus();
        });
    </script>
</body>
</html>