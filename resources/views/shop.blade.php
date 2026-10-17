@extends('layouts.app')

@section('content')
<div class="shop-page">
    <div class="container">

        <header class="shop-head">
            <div>
                <h1 class="shop-title">Shop</h1>
                <p class="shop-sub">Reserve online, pick up at the shop.</p>
            </div>

            <form method="GET" action="{{ route('shop') }}" class="shop-search" role="search">
                @if ($activeCategory)
                    <input type="hidden" name="category" value="{{ $activeCategory }}">
                @endif
                <i class="bi bi-search"></i>
                <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search products">
            </form>
        </header>

        <nav class="shop-chips" aria-label="Filter products by category">
            <a href="{{ route('shop', request()->only('q')) }}"
               class="shop-chip {{ $activeCategory ? '' : 'active' }}">All</a>
            @foreach ($categories as $category)
                <a href="{{ route('shop', array_merge(request()->only('q'), ['category' => $category->id])) }}"
                   class="shop-chip {{ $activeCategory === $category->id ? 'active' : '' }}">{{ $category->name }}</a>
            @endforeach
        </nav>

        <p class="shop-count">{{ $products->total() }} {{ $products->total() === 1 ? 'product' : 'products' }}</p>

        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
            @forelse ($products as $product)
                @include('partials.product-card', ['product' => $product])
            @empty
                <div class="col-12">
                    <div class="shop-empty">
                        <i class="bi bi-box-seam"></i>
                        <p class="mb-0">No products found. Try another search or category.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-4">{{ $products->links() }}</div>
    </div>
</div>
@endsection