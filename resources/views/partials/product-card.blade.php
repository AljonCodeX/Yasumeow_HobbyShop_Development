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

        {{-- Heart: add or remove from favourites --}}
        @auth
            @php $isFav = in_array($product->id, $favouriteIds ?? []); @endphp
            <form method="POST" action="{{ route('account.favourites.toggle', $product) }}" class="shop-heart-form">
                @csrf
                <button type="submit" class="shop-heart {{ $isFav ? 'is-on' : '' }}"
                        aria-label="{{ $isFav ? 'Remove from favourites' : 'Add to favourites' }}">
                    <i class="bi {{ $isFav ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                </button>
            </form>
        @else
            <div class="shop-heart-form">
                <a href="{{ route('login') }}" class="shop-heart" aria-label="Log in to add favourites">
                    <i class="bi bi-heart"></i>
                </a>
            </div>
        @endauth

        <div class="shop-item-body">
            <span class="shop-item-cat">{{ $product->category?->name ?? 'Others' }}</span>
            <a href="{{ route('products.show', $product) }}" class="shop-item-link">
                <h3 class="shop-item-name">{{ $product->name }}</h3>
            </a>

            <div class="shop-item-foot">
                <span class="shop-item-price">₱{{ number_format((float) $product->price, 2) }}</span>

                @if ($stock === 'out')
                    <span class="shop-item-stock stock-out">Sold out</span>
                @else
                    <span class="shop-item-sold">{{ number_format((int) ($product->sold_count ?? 0)) }} sold</span>
                @endif
            </div>
        </div>
    </div>
</div>