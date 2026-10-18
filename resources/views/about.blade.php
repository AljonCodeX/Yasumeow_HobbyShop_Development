@extends('layouts.app')

@section('content')
<div class="shop-page">
    <div class="container" style="max-width: 720px;">

        <header class="shop-head">
            <div>
                <h1 class="shop-title">About Us</h1>
                <p class="shop-sub">Yasumeow Hobby Shop</p>
            </div>
        </header>

        <div class="shop-empty">
            <i class="bi bi-hourglass-split"></i>
            <p class="fw-bold mb-1">Coming soon</p>
            <p class="mb-2">We are still working on this page. Check back soon!</p>
            <a href="{{ url('/') }}" class="fw-bold">Back to home</a>
        </div>
    </div>
</div>
@endsection