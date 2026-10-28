@extends('layouts.app')

@php
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
                            Almost<br>
                            <span class="text-info">there.</span>
                        </h2>
                        <p class="mb-0">Choose a new password and get back to your hobby.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="p-4 p-md-5">
                    <h1 class="h2 fw-bold text-center mb-4">Set a new password</h1>

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold small">{{ __('Email Address') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold small">New password</label>
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
                            Reset password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection