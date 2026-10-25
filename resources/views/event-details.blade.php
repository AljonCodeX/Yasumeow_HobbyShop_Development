@extends('layouts.app')

@section('content')
@php
    $left = $event->slotsLeft();
    $isPast = $event->event_date->isPast();
    $isOpen = $event->status === 'open' && ! $isPast;
@endphp
<div class="container pb-5">
    <nav class="pt-4 pb-3">
        <a href="{{ route('events') }}" class="small fw-bold text-body text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i>Back to Events
        </a>
    </nav>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="game-detail-hero overflow-hidden mb-4">
                @if ($event->image)
                    <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}" class="w-100 h-100 object-fit-cover">
                @else
                    <i class="bi {{ $event->game?->category?->icon ?? 'bi-calendar-event' }}"></i>
                @endif
            </div>

            @if ($event->game)
                <span class="badge text-bg-dark mb-2">{{ $event->game->name }}</span>
            @endif
            <h1 class="h3 fw-bold">{{ $event->title }}</h1>
            <p>{{ $event->description }}</p>

            <h2 class="h5 fw-bold text-uppercase mt-4 mb-3">
                <i class="bi bi-trophy me-2"></i>Results
            </h2>
            @if ($results->isEmpty())
                <div class="home-empty">
                    <i class="bi bi-trophy"></i>
                    <p class="mb-0">{{ $isPast ? 'Results have not been posted yet.' : 'Results will appear here after the event.' }}</p>
                </div>
            @else
                <div class="table-responsive shadow-sm">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">Place</th>
                                <th scope="col">Player</th>
                                <th scope="col" class="text-end">Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($results as $result)
                                <tr>
                                    <td class="fw-bold">#{{ $result->placement }}</td>
                                    <td>{{ $result->user?->name }}</td>
                                    <td class="text-end">{{ $result->points }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <p class="small text-muted mb-1">Date and time</p>
                    <p class="fw-bold mb-3">
                        <i class="bi bi-calendar-event me-1"></i>{{ $event->event_date->format('F j, Y') }}
                        <span class="d-block fw-normal"><i class="bi bi-clock me-1"></i>{{ $event->event_date->format('g:i A') }}</span>
                    </p>

                    <p class="small text-muted mb-1">Location</p>
                    <p class="fw-bold mb-3"><i class="bi bi-geo-alt me-1"></i>Yasumeow Hobby Shop</p>

                    <p class="small text-muted mb-1">Entry fee</p>
                    <p class="fw-bold mb-3">{{ $event->price > 0 ? '₱' . number_format((float) $event->price) : 'FREE' }}</p>

                    <p class="small text-muted mb-1">Slots</p>
                    <p class="fw-bold mb-3">{{ $event->taken_count }} / {{ $event->max_slots }} taken</p>

                    @if ($isRegistered)
                        <div class="alert alert-success py-2 small mb-2">
                            <i class="bi bi-check-circle me-1"></i>You are registered.
                        </div>
                        @unless ($isPast)
                            <form method="POST" action="{{ route('events.cancel', $event) }}"
                                  onsubmit="return confirm('Cancel your registration?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100">Cancel Registration</button>
                            </form>
                        @endunless
                    @elseif (! $isOpen)
                        <button class="btn btn-secondary btn-sm w-100" disabled>
                            {{ $isPast ? 'Event Ended' : 'Registration ' . $event->status }}
                        </button>
                    @elseif ($left === 0)
                        <button class="btn btn-secondary btn-sm w-100" disabled>Event Full</button>
                    @elseif (auth()->check())
                        <form method="POST" action="{{ route('events.register', $event) }}">
                            @csrf
                            <button type="submit" class="btn btn-dark btn-sm w-100">Register</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-dark btn-sm w-100">Log in to Register</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection