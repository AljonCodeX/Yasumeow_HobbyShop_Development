@php
    $ended = $ended ?? false;
    $left  = $ended ? 0 : $event->slotsLeft();
@endphp

<div class="col">
    <div class="shop-item {{ $ended ? 'is-out' : '' }}">
        <div class="shop-item-img position-relative">
            @if ($event->image)
                <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}">
            @else
                <i class="bi {{ $event->game?->category?->icon ?? 'bi-calendar-event' }}"></i>
            @endif
            <div class="event-badge">
                <small>{{ $event->event_date->format('M') }}</small>
                <strong>{{ $event->event_date->format('d') }}</strong>
            </div>
        </div>

        <div class="shop-item-body">
            <span class="shop-item-cat">{{ $event->game?->name ?? 'Event' }}</span>
            <h3 class="shop-item-name">
                <a href="{{ route('events.show', $event) }}" class="shop-item-link stretched-link">{{ $event->title }}</a>
            </h3>
            <p class="event-item-meta">
                <i class="bi bi-clock me-1"></i>{{ $event->event_date->format('M j, g:i A') }}
            </p>

            <div class="shop-item-foot">
                <span class="shop-item-price">
                    {{ $event->price > 0 ? '₱' . number_format((float) $event->price) : 'FREE' }}
                </span>

                @if ($ended)
                    <span class="shop-item-stock stock-out">Ended</span>
                @elseif ($event->status === 'cancelled')
                    <span class="shop-item-stock stock-out">Cancelled</span>
                @elseif ($event->status !== 'open')
                    <span class="shop-item-stock stock-low">{{ ucfirst($event->status) }}</span>
                @elseif ($left === 0)
                    <span class="shop-item-stock stock-out">Full</span>
                @elseif ($left <= 3)
                    <span class="shop-item-stock stock-low">Only {{ $left }} left</span>
                @else
                    <span class="shop-item-stock stock-in">{{ $left }} slots left</span>
                @endif
            </div>
        </div>
    </div>
</div>