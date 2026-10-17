@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-3">Shop</h1>

    <form method="GET" action="{{ route('shop') }}" class="row g-2 mb-3">
        @if ($activeCategory)
            <input type="hidden" name="category" value="{{ $activeCategory }}">
        @endif
        <div class="col-12 col-md-6">
            <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search products...">
        </div>
    </form>

    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('shop', request()->only('q')) }}"
           class="btn btn-sm {{ !$activeCategory ? 'btn-dark' : 'btn-outline-dark' }}">All</a>
        @foreach ($categories as $category)
            <a href="{{ route('shop', array_merge(request()->only('q'), ['category' => $category->id])) }}"
               class="btn btn-sm {{ $activeCategory === $category->id ? 'btn-dark' : 'btn-outline-dark' }}">
                {{ $category->name }}
            </a>
        @endforeach
    </div>

    <div class="row g-3">
        @forelse ($products as $product)
            @include('partials.product-card', ['product' => $product])
        @empty
            <p class="text-muted">No products found.</p>
        @endforelse
    </div>

    <div class="mt-4">{{ $products->links() }}</div>
</div>
@endsection