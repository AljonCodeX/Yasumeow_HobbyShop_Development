@extends('layouts.app')

@section('content')
@php
    $feeText = '₱' . number_format($fee, 2);
    $totalText = '₱' . number_format($total, 2);

    $options = [
        'store' => [
            'icon'   => 'bi-shop',
            'title'  => 'Pay in store',
            'amount' => null,
            'desc'   => 'No payment now. Pay the full ' . $totalText . ' when you pick up your items.',
            'now'    => 0,
            'later'  => $total,
            'button' => 'Confirm reservation',
        ],
        'fee' => [
            'icon'   => 'bi-bookmark-check',
            'title'  => 'Reservation fee',
            'amount' => $feeText,
            'desc'   => 'Pay a small fee online to hold your items. Pay the remaining ₱' . number_format($total - $fee, 2) . ' in store.',
            'now'    => $fee,
            'later'  => $total - $fee,
            'button' => 'Pay ' . $feeText . ' now',
        ],
        'online' => [
            'icon'   => 'bi-qr-code-scan',
            'title'  => 'Pay online in full',
            'amount' => $totalText,
            'desc'   => 'Scan a QR Ph code with GCash, Maya or your bank app. Just pick up at the shop.',
            'now'    => $total,
            'later'  => 0,
            'button' => 'Pay ' . $totalText . ' now',
        ],
    ];

    $selected = old('payment_option', 'store');
@endphp

<div class="shop-page">
    <div class="container" style="max-width: 1000px;">

        <a href="{{ route('shop') }}" class="product-back">
            <i class="bi bi-arrow-left me-1"></i>Back to shop
        </a>

        <header class="checkout-head">
            <h1 class="shop-title">Checkout</h1>
            <p class="shop-sub">Choose how you want to pay for your reservation.</p>
        </header>

        <form method="POST" action="{{ route('reserve.confirm') }}">
            @csrf

            <div class="checkout-grid">

                {{-- Payment options --}}
                <div>
                    <h2 class="checkout-section-title">Your details</h2>

                    <div class="account-card">
                        <div class="mb-3">
                            <label for="contact_name" class="form-label fw-bold small">Full name</label>
                            <input id="contact_name" type="text" name="contact_name"
                                value="{{ old('contact_name', Auth::user()->name) }}"
                                class="form-control @error('contact_name') is-invalid @enderror" required>
                            @error('contact_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="contact_phone" class="form-label fw-bold small">Mobile number</label>
                            <input id="contact_phone" type="tel" name="contact_phone"
                                value="{{ old('contact_phone', Auth::user()->phone) }}"
                                placeholder="09171234567"
                                class="form-control @error('contact_phone') is-invalid @enderror" required>
                            @error('contact_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label for="note" class="form-label fw-bold small">Note <span class="text-muted fw-normal">(optional)</span></label>
                            <textarea id="note" name="note" rows="2" maxlength="255"
                                    placeholder="Anything we should know? Example: I will pick up after 3 PM."
                                    class="form-control @error('note') is-invalid @enderror">{{ old('note') }}</textarea>
                            @error('note') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <h2 class="checkout-section-title mt-4">Payment method</h2>
                    @foreach ($options as $value => $option)
                        <label class="pay-option">
                            <input type="radio"
                                name="payment_option"
                                value="{{ $value }}"
                                data-now="{{ $option['now'] }}"
                                data-store="{{ $option['later'] }}"
                                data-button="{{ $option['button'] }}"
                                {{ $selected === $value ? 'checked' : '' }}>

                            <span class="pay-option-icon"><i class="bi {{ $option['icon'] }}"></i></span>

                            <span class="pay-option-body">
                                <span class="pay-option-title">
                                    <span>{{ $option['title'] }}</span>
                                    @if ($option['amount'])
                                        <span class="pay-option-amount">{{ $option['amount'] }}</span>
                                    @endif
                                </span>
                                <span class="pay-option-desc d-block">{{ $option['desc'] }}</span>
                            </span>

                            <span class="pay-option-check"><i class="bi bi-check-lg"></i></span>
                        </label>
                    @endforeach

                    @error('payment_option')
                        <p class="reserve-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Order summary --}}
                <aside class="checkout-summary">
                    <h2 class="checkout-section-title">Order summary</h2>

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
                                    <span class="reserve-name">{{ $p->name }}</span>
                                    <span class="shop-item-cat">Qty {{ $item['qty'] }}</span>
                                </div>
                                <span class="reserve-price">₱{{ number_format($item['subtotal'], 2) }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="checkout-totals">
                        <div class="checkout-total-row is-main">
                            <span>Total</span>
                            <span>{{ $totalText }}</span>
                        </div>
                        <div class="checkout-total-row is-now">
                            <span>Pay now</span>
                            <span id="checkoutNow">₱0.00</span>
                        </div>
                        <div class="checkout-total-row">
                            <span>Pay in store</span>
                            <span id="checkoutStore">{{ $totalText }}</span>
                        </div>
                    </div>

                    <button type="submit" id="checkoutButton" class="shop-item-btn checkout-btn">Confirm reservation</button>

                    <p class="checkout-note">
                        <i class="bi bi-clock-history"></i>
                        <span>Items paid online are held for 30 minutes. If payment is not completed, they are released.</span>
                    </p>
                </aside>

            </div>
        </form>
    </div>
</div>
@endsection