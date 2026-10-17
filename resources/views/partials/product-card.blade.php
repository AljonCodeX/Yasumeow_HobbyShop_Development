@php $stock = $product->stockStatus(); @endphp

<div class="col">
    <div class="shop-item {{ $stock === 'out' ? 'is-out' : '' }}">
        <a href="{{ route('products.show', $product) }}" class="shop-item-link">
            <div class="shop-item-img">
                @if ($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                @else
                    <i class="bi {{ $product->category?->icon ?? 'bi-box-seam' }}"></i>
                @endif
            </div>
        </a>

        <div class="shop-item-body">
            <span class="shop-item-cat">{{ $product->category?->name ?? 'Others' }}</span>
            <a href="{{ route('products.show', $product) }}" class="shop-item-link">
                <h3 class="shop-item-name">{{ $product->name }}</h3>
            </a>

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
    </div>
</div>