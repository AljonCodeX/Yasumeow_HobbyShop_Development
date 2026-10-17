@php $left = $event->slotsLeft(); @endphp

<div class="card h-100 border-0 shadow-sm overflow-hidden">
    <a href="{{ route('events.show', $event) }}" class="text-decoration-none">
        <div class="event-card-img position-relative">
            @if ($event->image)
                <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}">
            @else
                <i class="bi {{ $event->game?->icon ?? 'bi-calendar-event' }}"></i>
            @endif
            <div class="event-date">
                <small class="d-block fw-bold text-uppercase">{{ $event->event_date->format('M') }}</small>
                <span class="fs-5 fw-bold">{{ $event->event_date->format('d') }}</span>
            </div>
        </div>
    </a>
    <div class="card-body">
        <h3 class="h6 fw-bold">{{ $event->title }}</h3>
        @if ($event->game)
            <span class="badge text-bg-dark mb-2">{{ $event->game->name }}</span>
        @endif
        <p class="small text-muted mb-1">
            <i class="bi bi-clock me-1"></i>{{ $event->event_date->format('g:i A') }}
            &middot; <i class="bi bi-geo-alt me-1"></i>Yasumeow Hobby Shop
        </p>
        <div class="d-flex justify-content-between align-items-center">
            @if ($event->status !== 'open')
                <span class="small fw-bold text-secondary">{{ ucfirst($event->status) }}</span>
            @elseif ($left === 0)
                <span class="small fw-bold text-danger">Full</span>
            @else
                <span class="small text-muted">{{ $left }} {{ $left === 1 ? 'slot' : 'slots' }} left</span>
            @endif

            @if ($event->price > 0)
                <span class="badge bg-danger">₱{{ number_format((float) $event->price) }}</span>
            @else
                <span class="badge bg-success">FREE</span>
            @endif
        </div>
    </div>
    <div class="card-footer bg-transparent border-0 pt-0 pb-3">
        <a href="{{ route('events.show', $event) }}" class="btn btn-dark btn-sm w-100">View Event</a>
    </div>
</div>