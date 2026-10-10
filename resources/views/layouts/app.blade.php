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

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="Yasumeow" height="48" class="me-2">
                    <span class="fw-bold">Yasumeow Hobby Shop</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/games') }}">Games</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/shop') }}">Shop</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/events') }}">Events</a>
                        </li>

                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest

                        <!-- Search (last item) -->
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#searchModal" aria-label="Search">
                                <i class="bi bi-search"></i>
                                <span class="d-md-none ms-2">Search</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
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