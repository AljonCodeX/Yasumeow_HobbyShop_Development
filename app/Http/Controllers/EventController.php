<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Support\AdminAlert;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $all = Event::with('game.category')
            ->withCount(['registrations as taken_count' => function ($q) {
                $q->where('status', 'registered');
            }])
            ->upcoming()
            ->get();

        // Filter chips: only categories that have an upcoming event
        $categories = $all->map(fn ($e) => $e->game?->category)
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();

        $selected = $request->query('category');
        $events = $selected
            ? $all->filter(fn ($e) => \Illuminate\Support\Str::slug($e->game?->category?->name ?? '') === $selected)->values()
            : $all;

        $past = Event::with('game')
            ->where('status', '!=', 'cancelled')
            ->where(fn ($q) => $q->where('event_date', '<', now())->orWhere('status', 'completed'))
            ->orderByDesc('event_date')
            ->take(6)
            ->get();

        return view('events', compact('events', 'categories', 'selected', 'past'));
    }

    public function show(Event $event)
    {
        $event->load('game')->loadCount(['registrations as taken_count' => function ($q) {
            $q->where('status', 'registered');
        }]);

        $registration = Auth::check()
            ? $event->registrations()->where('user_id', Auth::id())->first()
            : null;

        $isRegistered = $registration?->status === 'registered';

        $results = $event->results()->with('user')->orderBy('placement')->take(10)->get();

        return view('event-details', compact('event', 'isRegistered', 'results'));
    }

    public function register(Event $event)
    {
        $userId = Auth::id();

        // Lock the event row so two people can't take the last slot at the same time
        [$type, $message] = DB::transaction(function () use ($event, $userId) {
            $event = Event::lockForUpdate()->findOrFail($event->id);

            if ($event->status !== 'open' || $event->event_date->isPast()) {
                return ['error', 'Registration for this event is closed.'];
            }

            $existing = Registration::where('event_id', $event->id)
                ->where('user_id', $userId)
                ->first();

            if ($existing && $existing->status === 'registered') {
                return ['error', 'You are already registered for this event.'];
            }

            if ($event->slotsLeft() === 0) {
                return ['error', 'Sorry, this event is full.'];
            }

            // Also re-activates someone who cancelled earlier
            Registration::updateOrCreate(
                ['event_id' => $event->id, 'user_id' => $userId],
                ['status' => 'registered', 'registered_at' => now()]
            );

            return ['success', 'You are registered for ' . $event->title . '!'];
        });

            if ($type === 'success') {
            AdminAlert::send(
                'New event registration',
                Auth::user()->name . ' registered for ' . $event->title,
                'heroicon-o-user-plus',
                'success'
            );
        }

            if ($type === 'success' && $event->slotsLeft() === 0) {
            AdminAlert::send(
                'Event full',
                $event->title . ' has no slots left.',
                'heroicon-o-ticket',
                'danger'
            );
        }

        return redirect()->route('events.show', $event)->with($type, $message);
    }

    public function cancel(Event $event)
    {
        $registration = Registration::where('event_id', $event->id)
            ->where('user_id', Auth::id())
            ->where('status', 'registered')
            ->first();

        if (! $registration) {
            return redirect()->route('events.show', $event)->with('error', 'You are not registered for this event.');
        }

        if ($event->event_date->isPast()) {
            return redirect()->route('events.show', $event)->with('error', 'This event has already started.');
        }

        $registration->update(['status' => 'cancelled']);

        AdminAlert::send(
            'Registration cancelled',
            Auth::user()->name . ' cancelled for ' . $event->title,
            'heroicon-o-user-minus',
            'warning'
        );

        return redirect()->route('events.show', $event)->with('success', 'Your registration was cancelled.');
    }
}