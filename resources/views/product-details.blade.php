@extends('layouts.app')

@section('content')
<div class="container py-4">
    <a href="{{ route('shop') }}" class="text-decoration-none">&larr; Back to shop</a>

    <div class="row g-4 mt-1">
        <div class="col-md-6">
            <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://placehold.co/600x450?text=No+Image' }}"
                 class="img-fluid rounded shadow-sm w-100" alt="{{ $product->name }}">
        </div>
        <div class="col-md-6">
            <small class="text-muted">{{ $product->category->name ?? 'Uncategorized' }}</small>
            <h1 class="h3 mt-1">{{ $product->name }}</h1>
            <p class="fs-4 fw-bold">₱{{ number_format($product->price, 2) }}</p>

            @if ($product->stock > 0)
                <span class="badge bg-success">{{ $product->stock }} in stock</span>
            @else
                <span class="badge bg-secondary">Out of stock</span>
            @endif

            <p class="mt-3">{{ $product->description }}</p>

            {{-- Step 2 will turn this into the real Reserve button --}}
            <button class="btn btn-dark" disabled>Reserve (coming soon)</button>
        </div>
    </div>

    @if ($related->isNotEmpty())
        <h2 class="h5 mt-5 mb-3">More in this category</h2>
        <div class="row g-3">
            @foreach ($related as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>
    @endif
</div>
@endsection