@extends('layouts.app')

@php
    // Finds every file in public/images that starts with "slide"
    $slides = collect(glob(public_path('images/slide*.*')))
        ->map(fn ($path) => basename($path))
        ->sort()
        ->values();
@endphp

@section('content')
<div class="container">
    <div class="card auth-card">
        <div class="row g-0">
            <div class="col-md-5 d-none d-md-block auth-side text-white">
                <div id="authSlides" class="carousel slide carousel-fade"
                    data-bs-ride="carousel" data-bs-interval="4000">
                    <div class="carousel-inner">
                        @foreach ($slides as $slide)
                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                <img src="{{ asset('images/' . $slide) }}" alt="Yasumeow Hobby Shop" class="auth-slide-img">
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="auth-side-overlay">
                    <div>
                        <h2 class="h3 fw-bold text-uppercase">
                            Your Hobby.<br>
                            <span class="text-info">Your Community.</span>
                        </h2>
                        <p class="mb-0">Log in to reserve event slots and your favorite hobby items.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="p-4 p-md-5">
                    <h1 class="h2 fw-bold text-center mb-4">Welcome back!</h1>
                    <h2 class="h5 fw-bold mb-3">Log in</h2>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold small">{{ __('Email Address') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold small">{{ __('Password') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label small" for="remember">
                                    {{ __('Remember Me') }}
                                </label>
                            </div>

                            @if (Route::has('password.request'))
                                <a class="small text-decoration-none" href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-info w-100 fw-bold">
                            {{ __('Login') }}
                        </button>
                    </form>

                    @if (Route::has('register'))
                        <p class="text-center mt-4 mb-0">
                            Don't have an account?
                            <a href="{{ route('register') }}" class="fw-bold text-decoration-none">Register</a>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection