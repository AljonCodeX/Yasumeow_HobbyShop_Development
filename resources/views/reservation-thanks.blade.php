@extends('layouts.app')

@section('content')
<div class="shop-page">
    <div class="container" style="max-width: 720px;">

        <h1 class="shop-title mb-3">Reservation #{{ $reservation->id }}</h1>

        @if ($reservation->status === 'awaiting_payment')
            <div class="shop-empty mb-4">
                <i class="bi bi-hourglass-split"></i>
                <p class="mb-0">We are waiting for PayMongo to confirm your payment. Refresh this page in a few seconds.</p>
            </div>
        @elseif ($reservation->status === 'cancelled')
            <div class="shop-empty mb-4">
                <i class="bi bi-x-circle"></i>
                <p class="mb-0">This reservation was cancelled.</p>
            </div>
        @else
            <div class="shop-empty mb-4">
                <i class="bi bi-check-circle"></i>
                <p class="mb-0">
                    Your reservation is confirmed. Pick it up at Yasumeow Hobby Shop.
                    @if ($reservation->amount_paid > 0)
                        You already paid ₱{{ number_format((float) $reservation->amount_paid, 2) }}.
                    @endif
                </p>
            </div>
        @endif

        <ul class="reserve-list mb-3">
            @foreach ($reservation->items as $item)
                <li class="reserve-row">
                    <div class="reserve-info">
                        <span class="reserve-name">{{ $products[$item->product_id]->name ?? 'Product' }}</span>
                        <span class="shop-item-cat">Qty {{ $item->quantity }}</span>
                    </div>
                    <span class="reserve-price">₱{{ number_format((float) $item->unit_price * $item->quantity, 2) }}</span>
                </li>
            @endforeach
        </ul>

        <div class="reserve-total">
            <span>Total</span>
            <span>₱{{ number_format((float) $reservation->amount_total, 2) }}</span>
        </div>

        @if ($reservation->status === 'pending')
            <p class="reserve-note mt-2">
                To pay in store: ₱{{ number_format((float) $reservation->amount_total - (float) $reservation->amount_paid, 2) }}
            </p>
        @endif

        <a href="{{ route('shop') }}" class="shop-item-btn mt-3">Back to shop</a>
    </div>
</div>
@endsection