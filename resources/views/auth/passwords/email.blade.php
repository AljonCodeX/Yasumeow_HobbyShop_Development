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
                            Forgot your<br>
                            <span class="text-info">password?</span>
                        </h2>
                        <p class="mb-0">No worries. We will email you a link to set a new one.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="p-4 p-md-5">
                    <h1 class="h2 fw-bold text-center mb-2">Reset password</h1>
                    <p class="text-center text-muted mb-4">Enter your email and we will send you a reset link.</p>

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="email" class="form-label fw-bold small">{{ __('Email Address') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="you@email.com" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-info w-100 fw-bold">
                            Send reset link
                        </button>
                    </form>

                    <p class="text-center mt-4 mb-0">
                        <a href="{{ route('login') }}" class="fw-bold text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i>Back to login
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection