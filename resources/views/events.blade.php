@extends('layouts.app')

@section('content')
<div class="shop-page">
    <div class="container">

        <header class="shop-head">
            <div>
                <h1 class="shop-title">Events</h1>
                <p class="shop-sub">Featuring Yasumeow Community League</p>
            </div>
        </header>

        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            {{-- Category chips --}}
            @if ($categories->count() > 1)
                <nav class="shop-chips" aria-label="Filter events by category">
                    <a href="{{ route('events', request()->only('sort')) }}"
                       class="shop-chip {{ $selected ? '' : 'active' }}">All</a>
                    @foreach ($categories as $category)
                        @php $slug = \Illuminate\Support\Str::slug($category->name); @endphp
                        <a href="{{ route('events', array_merge(request()->only('sort'), ['category' => $slug])) }}"
                           class="shop-chip {{ $selected === $slug ? 'active' : '' }}">{{ $category->name }}</a>
                    @endforeach
                </nav>
            @else
                <span></span>
            @endif

            {{-- Sort by date: nothing is selected until the visitor picks one --}}
            <form method="GET" action="{{ route('events') }}">
                @if ($selected)
                    <input type="hidden" name="category" value="{{ $selected }}">
                @endif
                <select name="sort" class="form-select form-select-sm" aria-label="Sort by date" onchange="this.form.submit()">
                    <option value="" disabled hidden @selected(! $sort)>Sort by date</option>
                    <option value="latest" @selected($sort === 'latest')>Latest</option>
                    <option value="oldest" @selected($sort === 'oldest')>Oldest</option>
                </select>
            </form>
        </div>

        <p class="shop-count">{{ $events->total() }} {{ $events->total() === 1 ? 'event' : 'events' }}</p>

        @php
            // Split this page into Upcoming and Past, keeping the order
            $groups = $events->getCollection()->groupBy(
                fn ($e) => ($e->event_date->isPast() || $e->status === 'completed') ? 'past' : 'upcoming'
            );
        @endphp

        @forelse ($groups as $group => $items)
            <h2 class="checkout-section-title {{ $loop->first ? 'mt-3' : 'mt-5' }}">
                {{ $group === 'past' ? 'Past events' : 'Upcoming events' }}
            </h2>
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3">
                @foreach ($items as $event)
                    @include('partials.event-item', ['event' => $event, 'ended' => $group === 'past'])
                @endforeach
            </div>
        @empty
            <div class="shop-empty">
                <i class="bi bi-calendar-x"></i>
                <p class="mb-0">No events found{{ $selected ? ' for this category' : '' }}.</p>
            </div>
        @endforelse

        <div class="mt-4">{{ $events->links() }}</div>
    </div>
</div>
@endsection