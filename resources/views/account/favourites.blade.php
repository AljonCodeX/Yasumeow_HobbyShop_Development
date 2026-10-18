@extends('account.layout')

@section('account')
<h2 class="checkout-section-title">Favourites</h2>

@if ($products->isEmpty())
    <div class="shop-empty">
        <i class="bi bi-heart"></i>
        <p class="mb-2">No favourites yet. Tap "Add to favourites" on a product.</p>
        <a href="{{ route('shop') }}" class="fw-bold">Browse the shop</a>
    </div>
@else
    <div class="row row-cols-2 row-cols-md-3 g-3">
        @foreach ($products as $product)
            @include('partials.product-card')
        @endforeach
    </div>
@endif
@endsection