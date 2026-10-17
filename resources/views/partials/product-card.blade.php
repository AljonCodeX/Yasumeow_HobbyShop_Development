@php $stock = $product->stockStatus(); @endphp

<div class="col">
    <a href="{{ route('products.show', $product) }}" class="shop-item {{ $stock === 'out' ? 'is-out' : '' }}">
        <div class="shop-item-img">
            @if ($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
            @else
                <i class="bi {{ $product->category?->icon ?? 'bi-box-seam' }}"></i>
            @endif
        </div>

        <div class="shop-item-body">
            <span class="shop-item-cat">{{ $product->category?->name ?? 'Others' }}</span>
            <h3 class="shop-item-name">{{ $product->name }}</h3>

            <div class="shop-item-foot">
                <span class="shop-item-price">₱{{ number_format((float) $product->price) }}</span>

                @if ($stock === 'in')
                    <span class="shop-item-stock stock-in">In stock</span>
                @elseif ($stock === 'low')
                    <span class="shop-item-stock stock-low">Only {{ $product->stock_qty }} left</span>
                @else
                    <span class="shop-item-stock stock-out">Sold out</span>
                @endif
            </div>
        </div>
    </a>
</div>