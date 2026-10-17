<div class="col-6 col-md-4 col-lg-3">
    <a href="{{ route('products.show', $product) }}" class="text-decoration-none text-dark">
        <div class="card h-100 shadow-sm">
            <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://placehold.co/400x300?text=No+Image' }}"
                 class="card-img-top" alt="{{ $product->name }}" style="aspect-ratio:4/3; object-fit:cover;">
            <div class="card-body">
                <small class="text-muted">{{ $product->category->name ?? 'Uncategorized' }}</small>
                <h6 class="card-title mt-1 mb-2">{{ $product->name }}</h6>
                <div class="d-flex justify-content-between align-items-center">
                    <strong>₱{{ number_format($product->price, 2) }}</strong>
                    @if ($product->stock <= 0)
                        <span class="badge bg-secondary">Out of stock</span>
                    @elseif ($product->stock <= 3)
                        <span class="badge bg-warning text-dark">Low stock</span>
                    @endif
                </div>
            </div>
        </div>
    </a>
</div>