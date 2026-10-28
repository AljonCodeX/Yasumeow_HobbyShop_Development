@extends('layouts.app')

@section('content')
@php
    $statuses = [
        'pending'          => ['Reserved', 'is-ok'],
        'awaiting_payment' => ['Awaiting payment', 'is-warn'],
        'ready'            => ['Ready for pickup', 'is-ok'],
        'picked_up'        => ['Picked up', 'is-ok'],
        'cancelled'        => ['Cancelled', 'is-bad'],
        'expired'          => ['Expired', 'is-bad'],
    ];
    [$statusLabel, $statusClass] = $statuses[$reservation->status] ?? [ucfirst($reservation->status), ''];

    $banners = [
        'awaiting_payment' => ['bi-hourglass-split', 'We are waiting for PayMongo to confirm your payment. Refresh this page in a few seconds.'],
        'pending'          => ['bi-check-circle', 'Your reservation is confirmed. Pick it up at Yasumeow Hobby Shop.'],
        'ready'            => ['bi-box-seam', 'Your items are ready. Pick them up at Yasumeow Hobby Shop.'],
        'picked_up'        => ['bi-bag-check', 'You picked up this reservation. Thank you!'],
        'cancelled'        => ['bi-x-circle', 'This reservation was cancelled.'],
        'expired'          => ['bi-calendar-x', 'This reservation expired because it was not picked up in time. The items were released.'],
    ];
    [$bannerIcon, $bannerText] = $banners[$reservation->status] ?? ['bi-info-circle', ''];

    $balance = max(0, (float) $reservation->amount_total - (float) $reservation->amount_paid);
    $isOpen  = in_array($reservation->status, ['pending', 'ready'], true);
@endphp

<div class="shop-page">
    <div class="container" style="max-width: 720px;">

        <div class="order-head">
            <div>
                <h1 class="shop-title mb-1">Reservation {{ $reservation->reference }}</h1>
                <p class="order-date">Reserved on {{ $reservation->reserved_at->format('M j, Y g:i A') }}</p>
            </div>
            <span class="account-status {{ $statusClass }}">{{ $statusLabel }}</span>
        </div>

        @if ($bannerText)
            <div class="order-banner">
                <i class="bi {{ $bannerIcon }}"></i>
                <p>{{ $bannerText }}</p>
            </div>
        @endif

        {{-- Items --}}
        <h2 class="checkout-section-title">Items</h2>
        <ul class="reserve-list mb-3">
            @foreach ($reservation->items as $item)
                @php $p = $item->product; @endphp
                <li class="reserve-row">
                    <div class="reserve-thumb">
                        @if ($p?->image)
                            <img src="{{ asset('storage/' . $p->image) }}" alt="{{ $p->name }}">
                        @else
                            <i class="bi {{ $p?->category?->icon ?? 'bi-box-seam' }}"></i>
                        @endif
                    </div>
                    <div class="reserve-info">
                        @if ($p)
                            <a href="{{ route('products.show', $p) }}" class="reserve-name">{{ $p->name }}</a>
                        @else
                            <span class="reserve-name">Product</span>
                        @endif
                        <span class="shop-item-cat">₱{{ number_format((float) $item->unit_price, 2) }} × {{ $item->quantity }}</span>
                    </div>
                    <span class="reserve-price">₱{{ number_format((float) $item->unit_price * $item->quantity, 2) }}</span>
                </li>
            @endforeach
        </ul>

        {{-- Payment --}}
        <div class="account-card">
            <h2 class="checkout-section-title">Payment</h2>

            <div class="order-row">
                <span>Payment method</span>
                <span>{{ $reservation->payment_method === 'online' ? 'QR Ph' : 'Pay in store' }}</span>
            </div>
            <div class="order-row">
                <span>Total</span>
                <span>₱{{ number_format((float) $reservation->amount_total, 2) }}</span>
            </div>
            <div class="order-row">
                <span>Paid</span>
                <span>₱{{ number_format((float) $reservation->amount_paid, 2) }}</span>
            </div>

            @if ($isOpen)
                <div class="order-row is-due">
                    <span>To pay in store</span>
                    <span>₱{{ number_format($balance, 2) }}</span>
                </div>
            @endif

            @if ($reservation->status === 'pending' && $reservation->pickup_by)
                <div class="order-row">
                    <span>Pick up by</span>
                    <span>{{ $reservation->pickup_by->format('M j, Y g:i A') }}</span>
                </div>
                <p class="reserve-note mt-2 mb-0">
                    <i class="bi bi-clock-history me-1"></i>After that time the reservation expires and the items are released.
                </p>
            @endif
        </div>

        {{-- Contact --}}
        <div class="account-card">
            <h2 class="checkout-section-title">Contact</h2>

            <div class="order-row">
                <span>Name</span>
                <span>{{ $reservation->contact_name ?: '—' }}</span>
            </div>
            <div class="order-row">
                <span>Phone</span>
                <span>{{ $reservation->contact_phone ?: '—' }}</span>
            </div>
            @if ($reservation->note)
                <div class="order-row">
                    <span>Note</span>
                    <span>{{ $reservation->note }}</span>
                </div>
            @endif
        </div>

        @if ($reservation->canCustomerCancel())
            <form method="POST" action="{{ route('reservations.cancel', $reservation) }}"
                  onsubmit="return confirm('Cancel this reservation? The items will be released.')">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-outline-danger w-100 mt-3">
                    <i class="bi bi-x-circle me-1"></i>Cancel reservation
                </button>
            </form>
        @elseif (in_array($reservation->status, ['pending', 'ready'], true))
            <p class="reserve-note mt-3 mb-0">
                <i class="bi bi-chat-dots me-1"></i>Paid online reservations can't be cancelled here.
                <a href="{{ config('shop.messenger') }}" target="_blank" rel="noopener" class="fw-bold">Message us on Messenger</a> to cancel.
            </p>
        @endif

        <div class="order-actions">
            <a href="{{ route('account.reservations') }}" class="shop-item-btn">My reservations</a>
            <a href="{{ route('shop') }}" class="shop-item-btn is-primary">Back to shop</a>
        </div>
    </div>
</div>
@endsection