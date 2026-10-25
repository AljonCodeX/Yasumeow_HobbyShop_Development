@extends('layouts.app')

@php
    // Same slideshow images as the login page
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
                            Join the<br>
                            <span class="text-info">Fun.</span>
                        </h2>
                        <p class="mb-0">Create an account to join events and reserve your favorite hobby items.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="p-4 p-md-5">
                    <h1 class="h2 fw-bold text-center mb-4">Create your account</h1>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold small">{{ __('Name') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="Your name" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold small">{{ __('Email Address') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="you@email.com" required autocomplete="email">

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
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="At least 8 characters" required autocomplete="new-password">
                                <button type="button" class="input-group-text password-toggle" aria-label="Show password"><i class="bi bi-eye"></i></button>

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-text">At least 8 characters.</div>
                        </div>

                        <div class="mb-4">
                            <label for="password-confirm" class="form-label fw-bold small">{{ __('Confirm Password') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Repeat your password" required autocomplete="new-password">
                                <button type="button" class="input-group-text password-toggle" aria-label="Show password"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-info w-100 fw-bold">
                            {{ __('Register') }}
                        </button>
                    </form>

                    <div class="d-flex align-items-center my-3">
                        <hr class="flex-grow-1 m-0">
                        <span class="px-3 small text-muted">or</span>
                        <hr class="flex-grow-1 m-0">
                    </div>

                    <a href="{{ route('google.login') }}" class="btn btn-outline-dark w-100 fw-bold d-flex justify-content-center align-items-center gap-2">
                        <svg width="18" height="18" viewBox="0 0 48 48" aria-hidden="true">
                            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                        </svg>
                        Continue with Google
                    </a>

                    <p class="text-center mt-4 mb-0">
                        Already have an account?
                        <a href="{{ route('login') }}" class="fw-bold text-decoration-none">Log in</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection