@extends('layouts.app')

@section('content')
<div class="shop-page">
    <div class="container" style="max-width: 720px;">

        <a href="{{ route('shop') }}" class="product-back">
            <i class="bi bi-arrow-left me-1"></i>Back to shop
        </a>

        <h1 class="shop-title mb-3">Checkout</h1>

        {{-- Order summary --}}
        <ul class="reserve-list mb-4">
            @foreach ($items as $item)
                <li class="reserve-row">
                    <div class="reserve-info">
                        <span class="reserve-name">{{ $item['product']->name }}</span>
                        <span class="shop-item-cat">Qty {{ $item['qty'] }}</span>
                    </div>
                    <span class="reserve-price">₱{{ number_format($item['subtotal'], 2) }}</span>
                </li>
            @endforeach
        </ul>

        <div class="reserve-total mb-4">
            <span>Total</span>
            <span>₱{{ number_format($total, 2) }}</span>
        </div>

        {{-- Payment options --}}
        <form method="POST" action="{{ route('reserve.confirm') }}">
            @csrf

            <h2 class="h6 text-uppercase mb-3">How do you want to pay?</h2>

            <label class="shop-item p-3 d-block mb-2">
                <input class="form-check-input me-2" type="radio" name="payment_option" value="store" checked>
                <strong>Pay in store</strong>
                <span class="d-block shop-item-cat mt-1">No payment now. Pay the full ₱{{ number_format($total, 2) }} when you pick up.</span>
            </label>

            <label class="shop-item p-3 d-block mb-2">
                <input class="form-check-input me-2" type="radio" name="payment_option" value="fee">
                <strong>Pay a reservation fee: ₱{{ number_format($fee, 2) }}</strong>
                <span class="d-block shop-item-cat mt-1">Pay the fee online now to hold your items. Pay the rest in store.</span>
            </label>

            <label class="shop-item p-3 d-block mb-3">
                <input class="form-check-input me-2" type="radio" name="payment_option" value="online">
                <strong>Pay online: ₱{{ number_format($total, 2) }}</strong>
                <span class="d-block shop-item-cat mt-1">Pay in full with GCash, QR Ph or card. Just pick up at the shop.</span>
            </label>

            @error('payment_option')
                <p class="reserve-error">{{ $message }}</p>
            @enderror

            <button type="submit" class="shop-item-btn">Continue</button>
            <p class="reserve-note mt-2">Items paid online are held for 30 minutes. If payment is not completed, they are released.</p>
        </form>
    </div>
</div>
@endsection