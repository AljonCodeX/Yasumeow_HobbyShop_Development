@extends('layouts.app')

@section('content')
@php $stock = $product->stockStatus(); @endphp

<div class="shop-page">
    <div class="container">

        <a href="{{ route('shop') }}" class="product-back">
            <i class="bi bi-arrow-left me-1"></i>Back to shop
        </a>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="product-photo">
                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                    @else
                        <i class="bi {{ $product->category?->icon ?? 'bi-box-seam' }}"></i>
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <span class="shop-item-cat">{{ $product->category?->name ?? 'Others' }}</span>
                <h1 class="product-name">{{ $product->name }}</h1>
                <p class="product-price">₱{{ number_format((float) $product->price, 2) }}</p>

                @if ($stock === 'in')
                    <span class="shop-item-stock stock-in">In stock</span>
                @elseif ($stock === 'low')
                    <span class="shop-item-stock stock-low">Only {{ $product->stock_qty }} left</span>
                @else
                    <span class="shop-item-stock stock-out">Sold out</span>
                @endif

                <p class="product-desc">{{ $product->description ?: 'Available at Yasumeow Hobby Shop.' }}</p>

                <div class="product-reserve">
                    @if ($stock === 'out')
                        <button type="button" class="shop-item-btn" disabled>Sold out</button>
                    @elseif (auth()->check())
                        <form method="POST" action="{{ route('reserve.add', $product) }}">
                            @csrf
                            <button type="submit" class="shop-item-btn">Reserve</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="shop-item-btn">Log in to reserve</a>
                    @endif
                </div>

                <ul class="product-info">
                    <li><i class="bi bi-geo-alt"></i>Pick up at Yasumeow Hobby Shop</li>
                    <li><i class="bi bi-wallet2"></i>Pay in store or GCash</li>
                </ul>
            </div>
        </div>

        @if ($related->isNotEmpty())
            <h2 class="product-related-title">More in this category</h2>
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
                @foreach ($related as $item)
                    @include('partials.product-card', ['product' => $item])
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection