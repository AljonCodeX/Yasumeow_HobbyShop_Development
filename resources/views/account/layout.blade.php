@extends('layouts.app')

@section('content')
@php
    $user = Auth::user();
    $menu = [
        ['route' => 'account.dashboard',    'icon' => 'bi-grid',      'label' => 'Dashboard'],
        ['route' => 'account.reservations', 'icon' => 'bi-bag-check', 'label' => 'Reservations'],
        ['route' => 'account.info',         'icon' => 'bi-person',    'label' => 'Account Info'],
        ['route' => 'account.favourites',   'icon' => 'bi-heart',     'label' => 'Favourites'],
        ['route' => 'account.appearance',   'icon' => 'bi-palette',   'label' => 'Appearance'],
    ];
@endphp

<div class="shop-page">
    <div class="container" style="max-width: 1000px;">
        <div class="account-grid">

            <aside>
                <h2 class="checkout-section-title">Menu</h2>
                <nav class="account-menu">
                    @foreach ($menu as $item)
                        <a href="{{ route($item['route']) }}"
                           class="account-link {{ request()->routeIs($item['route']) ? 'active' : '' }}">
                            <i class="bi {{ $item['icon'] }}"></i>{{ $item['label'] }}
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
                <div class="account-profile">
                    @if ($user->profile_photo)
                        <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="account-avatar">
                    @else
                        <span class="account-avatar is-icon"><i class="bi bi-person"></i></span>
                    @endif
                    <h1 class="account-name">{{ $user->name }}</h1>
                    <p class="account-email">{{ $user->email }}</p>
                    <span class="account-badge">{{ $user->is_admin ? 'Admin' : 'Member' }}</span>
                </div>

                @yield('account')
            </section>
        </div>
    </div>
</div>
@endsection