@extends('layouts.app')

@section('content')
@php
    $user = Auth::user();
    $favMenuCount = $user->newFavouritesCount('fav_menu_seen_at');
    $menu = [
        ['route' => 'account.dashboard',    'icon' => 'bi-grid',      'label' => 'Dashboard'],
        ['route' => 'account.info',         'icon' => 'bi-person',    'label' => 'Account Info'],
        ['route' => 'account.favourites',   'icon' => 'bi-heart',     'label' => 'Favourites'],
        ['route' => 'account.reservations', 'icon' => 'bi-bag-check', 'label' => 'Reservations'],
        ['route' => 'account.appearance',   'icon' => 'bi-palette',   'label' => 'Appearance'],
    ];
@endphp

<div class="shop-page">
    <div class="container" style="max-width: 1000px;">
        <div class="account-grid">

            <aside class="d-none d-lg-block">
                <h2 class="checkout-section-title">Menu</h2>
                <nav class="account-menu">
                    @foreach ($menu as $item)
                        <a href="{{ route($item['route']) }}"
                           class="account-link {{ request()->routeIs($item['route']) ? 'active' : '' }}">
                            <i class="bi {{ $item['icon'] }}"></i>{{ $item['label'] }}
                            @if ($item['route'] === 'account.favourites' && $favMenuCount > 0)
                                <span class="menu-badge">{{ $favMenuCount }}</span>
                            @endif
                        </a>
                    @endforeach

                    @if ($user->is_admin)
                        <a href="{{ url('/admin') }}" class="account-link">
                            <i class="bi bi-speedometer2"></i>Admin Panel
                        </a>
                    @endif

                    <a href="{{ route('logout') }}" class="account-link"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right"></i>Log out
                    </a>
                </nav>
            </aside>

            <section>
                @php $editable = request()->routeIs('account.info'); @endphp

                @if ($editable)
                    {{-- Account Info page: profile on the left, camera icon changes the photo --}}
                    <div class="account-profile is-left">
                        <div class="account-avatar-wrap">
                            @if ($user->profile_photo)
                                <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="account-avatar">
                            @else
                                <span class="account-avatar is-icon"><i class="bi bi-person"></i></span>
                            @endif

                            <form id="photoForm" method="POST" action="{{ route('account.photo.update') }}" enctype="multipart/form-data">
                                @csrf
                                <input type="file" name="photo" id="photoInput" accept="image/*" class="d-none">
                                <button type="button" id="photoButton" class="account-avatar-edit" aria-label="Change profile photo">
                                    <i class="bi bi-camera-fill"></i>
                                </button>
                            </form>
                        </div>

                        <div class="account-profile-text">
                            <h1 class="account-name">{{ $user->name }}</h1>
                            <p class="account-email">{{ $user->email }}</p>
                            <span class="account-badge">{{ $user->is_admin ? 'Admin' : 'Member' }}</span>

                            @error('photo')
                                <p class="text-danger small mt-2 mb-0">{{ $message }}</p>
                            @enderror

                            @if ($user->profile_photo)
                                <form method="POST" action="{{ route('account.photo.remove') }}" class="mt-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link btn-sm p-0 text-body-secondary">Remove photo</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="account-profile">
                        <a href="{{ route('account.info') }}" class="account-profile-link" aria-label="Edit account info">
                            @if ($user->profile_photo)
                                <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="account-avatar">
                            @else
                                <span class="account-avatar is-icon"><i class="bi bi-person"></i></span>
                            @endif
                            <h1 class="account-name">{{ $user->name }}</h1>
                            <p class="account-email">{{ $user->email }}</p>
                        </a>
                        <span class="account-badge">{{ $user->is_admin ? 'Admin' : 'Member' }}</span>
                    </div>
                @endif

                @yield('account')
            </section>
        </div>
    </div>
</div>
@endsection