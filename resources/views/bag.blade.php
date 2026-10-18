@extends('layouts.app')

@section('content')
<div class="shop-page">
    <div class="container">

        <header class="checkout-head">
            <h1 class="shop-title">My Bag</h1>
            <p class="shop-sub">Review your items before checkout.</p>
        </header>

        @if ($items->isEmpty())
            <div class="shop-empty">
                <i class="bi bi-bag"></i>
                <p class="fw-bold mb-1">Your bag is empty</p>
                <p class="mb-2">Pick something from the shop and it will show up here.</p>
                <a href="{{ route('shop') }}" class="fw-bold">Browse the shop</a>
            </div>
        @else
            <div class="checkout-grid">

                <ul class="reserve-list">
                    @foreach ($items as $item)
                        @php $p = $item['product']; @endphp
                        <li class="reserve-row">
                            <div class="reserve-thumb">
                                @if ($p->image)
                                    <img src="{{ asset('storage/' . $p->image) }}" alt="{{ $p->name }}">
                                @else
                                    <i class="bi {{ $p->category?->icon ?? 'bi-box-seam' }}"></i>
                                @endif
                            </div>

                            <div class="reserve-info">
                                <a href="{{ route('products.show', $p) }}" class="reserve-name">{{ $p->name }}</a>
                                <span class="reserve-price">₱{{ number_format($item['subtotal']) }}</span>

                                <div class="reserve-qty">
                                    <form method="POST" action="{{ route('reserve.decrease', $p) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="reserve-qty-btn" aria-label="Decrease quantity"><i class="bi bi-dash"></i></button>
                                    </form>
                                    <span>{{ $item['qty'] }}</span>
                                    <form method="POST" action="{{ route('reserve.increase', $p) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="reserve-qty-btn" aria-label="Increase quantity"><i class="bi bi-plus"></i></button>
                                    </form>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('reserve.remove', $p) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="reserve-remove" aria-label="Remove {{ $p->name }}"><i class="bi bi-trash"></i></button>
                            </form>
                        </li>
                    @endforeach
                </ul>

                <aside class="checkout-summary">
                    <h2 class="checkout-section-title">Summary</h2>

                    <div class="checkout-totals">
                        <div class="checkout-total-row is-main">
                            <span>Total</span>
                            <span>₱{{ number_format($total, 2) }}</span>
                        </div>
                    </div>

                    <a href="{{ route('reserve.checkout') }}" class="shop-item-btn checkout-btn">Proceed to checkout</a>
                </aside>

            </div>
        @endif
    </div>
</div>
@endsection