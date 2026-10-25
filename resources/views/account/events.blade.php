@extends('account.layout')

@section('account')
@php
    $chips = ['all' => 'All', 'upcoming' => 'Upcoming', 'past' => 'Past', 'cancelled' => 'Cancelled'];
    $current = $filter ?? 'all';
@endphp

<h2 class="history-title">My Events</h2>

<nav class="shop-chips" aria-label="Filter events">
    @foreach ($chips as $key => $label)
        <a href="{{ route('account.events', $key === 'all' ? [] : ['filter' => $key]) }}"
           class="shop-chip {{ $current === $key ? 'active' : '' }}">{{ $label }}</a>
    @endforeach
</nav>

@forelse ($registrations as $registration)
    @php
        $event  = $registration->event;
        $result = $results->get($registration->event_id);
        $ended  = $event->event_date->isPast() || $event->status === 'completed';

        if ($registration->status === 'cancelled') {
            [$label, $class] = ['You cancelled', 'is-bad'];
        } elseif ($event->status === 'cancelled') {
            [$label, $class] = ['Event cancelled', 'is-bad'];
        } elseif ($result) {
            [$label, $class] = ['Placed #' . $result->placement, 'is-ok'];
        } elseif ($ended) {
            [$label, $class] = ['Ended', ''];
        } else {
            [$label, $class] = ['Registered', 'is-ok'];
        }
    @endphp

    <a href="{{ route('events.show', $event) }}" class="history-card">
        <div class="history-card-title">{{ $event->title }}</div>

        <div class="history-card-body">
            @if ($event->game)
                <div class="history-item">{{ $event->game->name }}</div>
            @endif
            <div class="history-item">
                <small>Entry fee:</small>
                {{ $event->price > 0 ? '₱' . number_format((float) $event->price) : 'FREE' }}
            </div>
            @if ($result)
                <div class="history-item">
                    <small>Result:</small> #{{ $result->placement }} · {{ $result->points }} pts
                </div>
            @endif

            <div class="history-card-foot">
                <span>{{ $event->event_date->format('M j, Y g:i A') }}</span>
                <span class="history-status {{ $class }}">{{ $label }}</span>
            </div>
        </div>
    </a>
@empty
    <div class="shop-empty">
        <i class="bi bi-calendar-x"></i>
        <p class="mb-2">{{ $filter ? 'No events match this filter.' : 'You have not registered for any events yet.' }}</p>
        <a href="{{ route('events') }}" class="fw-bold">Browse events</a>
    </div>
@endforelse

<div class="mt-3">{{ $registrations->links() }}</div>
@endsection