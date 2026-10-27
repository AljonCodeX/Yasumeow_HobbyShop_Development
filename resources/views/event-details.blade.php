@extends('layouts.app')

@section('content')
@php
    $left    = $event->slotsLeft();
    $isPast  = $event->event_date->isPast() || $event->status === 'completed';
    $isOpen  = $event->status === 'open' && ! $isPast;
    $taken   = (int) $event->taken_count;
    $percent = $event->max_slots > 0 ? min(100, (int) round($taken / $event->max_slots * 100)) : 0;
    $meter   = $left === 0 ? 'is-full' : ($percent >= 70 ? 'is-warn' : '');
    $podium  = $results->take(3)->values();
    $rest    = $results->slice(3);
    $medals  = ['is-gold', 'is-silver', 'is-bronze'];
@endphp

<div class="shop-page">
    <div class="container" style="max-width: 1100px;">

        <a href="{{ route('events') }}" class="product-back">
            <i class="bi bi-arrow-left me-1"></i>Back to Events
        </a>

        <div class="event-layout">

            {{-- Hero + about --}}
            <div class="event-main">
                <div class="event-hero">
                    @if ($event->image)
                        <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}">
                    @else
                        <span class="event-hero-icon">
                            <i class="bi {{ $event->game?->category?->icon ?? 'bi-calendar-event' }}"></i>
                        </span>
                    @endif
                </div>

                <div class="event-heading">
                    @if ($event->game)
                        <span class="event-hero-tag">{{ $event->game->name }}</span>
                    @endif
                    <h1 class="event-hero-title">{{ $event->title }}</h1>
                </div>

                <h2 class="checkout-section-title mt-4">About this event</h2>
                <p class="event-about">{{ $event->description ?: 'Join us at Yasumeow Hobby Shop.' }}</p>
            </div>

            {{-- Details + register --}}
            <aside class="event-side">
                <div class="event-card">
                    <div class="event-fact">
                        <span class="event-fact-icon"><i class="bi bi-calendar-event"></i></span>
                        <div>
                            <span class="event-fact-label">Date and time</span>
                            <span class="event-fact-value">{{ $event->event_date->format('F j, Y') }}</span>
                            <span class="event-fact-sub">{{ $event->event_date->format('g:i A') }}</span>
                        </div>
                    </div>

                    <div class="event-fact">
                        <span class="event-fact-icon"><i class="bi bi-geo-alt"></i></span>
                        <div>
                            <span class="event-fact-label">Location</span>
                            <span class="event-fact-value">Yasumeow Hobby Shop</span>
                        </div>
                    </div>

                    <div class="event-fact">
                        <span class="event-fact-icon"><i class="bi bi-ticket-perforated"></i></span>
                        <div>
                            <span class="event-fact-label">Entry fee</span>
                            <span class="event-fact-value is-fee">
                                {{ $event->price > 0 ? '₱' . number_format((float) $event->price) : 'FREE' }}
                            </span>
                        </div>
                    </div>

                    <div class="event-fact">
                        <span class="event-fact-icon"><i class="bi bi-people"></i></span>
                        <div class="flex-grow-1">
                            <span class="event-fact-label">Slots</span>
                            <span class="event-fact-value">{{ $taken }} / {{ $event->max_slots }} taken</span>
                            <div class="event-meter {{ $meter }}"><span @style(['width: ' . $percent . '%'])></span></div>
                            <span class="event-fact-sub">
                                {{ $left === 0 ? 'No slots left' : $left . ($left === 1 ? ' slot left' : ' slots left') }}
                            </span>
                        </div>
                    </div>

                    <div class="event-action">
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
                            <button class="btn btn-secondary w-100" disabled>
                                {{ $isPast ? 'Event Ended' : 'Registration ' . $event->status }}
                            </button>
                        @elseif ($left === 0)
                            <button class="btn btn-secondary w-100" disabled>Event Full</button>
                        @elseif (auth()->check())
                            <form method="POST" action="{{ route('events.register', $event) }}">
                                @csrf
                                <button type="submit" class="btn btn-info fw-bold w-100">Register</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-info fw-bold w-100">Log in to Register</a>
                        @endif
                    </div>
                </div>
            </aside>

            {{-- Results --}}
            <section class="event-results">
                <h2 class="checkout-section-title">Results</h2>

                @if ($event->result_image)
                    <a href="{{ asset('storage/' . $event->result_image) }}" target="_blank" rel="noopener"
                       class="event-result-photo" title="Open full size">
                        <img src="{{ asset('storage/' . $event->result_image) }}"
                             alt="{{ $event->title }} results" loading="lazy">
                    </a>
                @endif

                @if ($results->isEmpty())
                    <div class="event-empty">
                        <i class="bi bi-trophy"></i>
                        <span>{{ $isPast ? 'Results have not been posted yet.' : 'Results will appear here after the event.' }}</span>
                    </div>
                @else
                    <div class="event-podium">
                        @foreach ($podium as $result)
                            <div class="event-podium-item {{ $medals[$loop->index] }}">
                                <i class="bi bi-trophy-fill"></i>
                                <span class="event-podium-place">#{{ $result->placement }}</span>
                                <span class="event-podium-name">{{ $result->user?->name }}</span>
                                <span class="event-podium-points">{{ $result->points }} pts</span>
                            </div>
                        @endforeach
                    </div>

                    @if ($rest->isNotEmpty())
                        <div class="event-card p-0 mt-3 overflow-hidden">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">Place</th>
                                            <th scope="col">Player</th>
                                            <th scope="col" class="text-end">Points</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rest as $result)
                                            <tr>
                                                <td class="fw-bold">#{{ $result->placement }}</td>
                                                <td>{{ $result->user?->name }}</td>
                                                <td class="text-end">{{ $result->points }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                @endif
            </section>

        </div>
    </div>
</div>
@endsection