@extends('account.layout')

@section('account')
@php
    $statuses = [
        'pending'          => ['Reserved', 'is-ok'],
        'awaiting_payment' => ['Awaiting payment', 'is-warn'],
        'ready'            => ['Ready for pickup', 'is-ok'],
        'picked_up'        => ['Picked up', 'is-ok'],
        'cancelled'        => ['Cancelled', 'is-bad'],
        'expired'          => ['Expired', 'is-bad'],
    ];

    $statusOptions = [
        'pending'          => ['Reserved', 'is-ok'],
        'awaiting_payment' => ['Awaiting payment', 'is-warn'],
        'ready'            => ['Ready for pickup', 'is-info'],
        'picked_up'        => ['Picked up', 'is-ok'],
        'cancelled'        => ['Cancelled', 'is-bad'],
    ];
    $currentStatus = request('status');

    $hasFilter = request()->hasAny(['status', 'from', 'to', 'q']);
@endphp

<h2 class="history-title">Reservation History</h2>

<form method="GET" action="{{ route('account.reservations') }}" id="historyForm" class="history-filters">
    <input type="text" id="dateRange" class="form-control history-range" placeholder="Select date range" readonly>
    <input type="hidden" name="from" id="dateFrom" value="{{ request('from') }}">
    <input type="hidden" name="to" id="dateTo" value="{{ request('to') }}">

    <div class="history-row">
<div class="dropdown status-dropdown">
    <button type="button" class="status-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        <span id="statusLabel">{{ $currentStatus && isset($statusOptions[$currentStatus]) ? $statusOptions[$currentStatus][0] : 'Status' }}</span>
        <i class="bi bi-chevron-down"></i>
    </button>

    <ul class="dropdown-menu status-menu">
        <li>
            <button type="button" class="status-item {{ $currentStatus ? '' : 'active' }}" data-value="" data-label="Status">
                <span class="status-dot is-all"></span>All statuses
                <i class="bi bi-check2 ms-auto"></i>
            </button>
        </li>
        @foreach ($statusOptions as $value => [$label, $dot])
            <li>
                <button type="button" class="status-item {{ $currentStatus === $value ? 'active' : '' }}"
                        data-value="{{ $value }}" data-label="{{ $label }}">
                    <span class="status-dot {{ $dot }}"></span>{{ $label }}
                    <i class="bi bi-check2 ms-auto"></i>
                </button>
            </li>
        @endforeach
    </ul>

    <input type="hidden" name="status" id="statusInput" value="{{ $currentStatus }}">
</div>

        <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search" aria-label="Search">
    </div>
</form>

<div id="historyList">
@forelse ($reservations as $reservation)
    @php [$label, $class] = $statuses[$reservation->status] ?? [ucfirst($reservation->status), '']; @endphp

    <a href="{{ route('reservations.thanks', $reservation) }}" class="history-card">
        <div class="history-card-title">Reservation {{ $reservation->reference }}</div>

        <div class="history-card-body">
            @foreach ($reservation->items as $item)
                <div class="history-item">
                    {{ $item->product?->name ?? 'Product' }}
                    <small>× {{ $item->quantity }}</small>
                </div>
            @endforeach

            <div class="history-card-foot">
                <span>{{ $reservation->reserved_at->format('d-m-Y H:i') }}</span>
                <span class="history-status {{ $class }}">{{ $label }}</span>
            </div>
        </div>
    </a>
@empty
    <div class="shop-empty">
        <i class="bi bi-bag"></i>
        <p class="mb-2">{{ $hasFilter ? 'No reservations match your filters.' : 'You have no reservations yet.' }}</p>
        <a href="{{ route('shop') }}" class="fw-bold">Browse the shop</a>
    </div>
@endforelse

<div class="mt-3">{{ $reservations->links() }}</div>
</div>
@endsection