<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Support\AdminAlert;
use App\Models\Category;
use App\Models\Game;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $selected = $request->query('category');
        $sort = in_array($request->query('sort'), ['latest', 'oldest'], true)
            ? $request->query('sort')
            : null;

        // Filter chips: categories that have any event
        $categories = Category::whereIn('id', Game::whereIn('id', Event::select('game_id'))->select('category_id'))
            ->orderBy('name')
            ->get();
        $category = $selected
            ? $categories->first(fn ($c) => Str::slug($c->name) === $selected)
            : null;

        $query = Event::with('game.category')
            ->withCount(['registrations as taken_count' => function ($q) {
                $q->where('status', 'registered');
            }])
            ->where('status', '!=', 'cancelled');

        if ($category) {
            $query->whereHas('game', fn ($q) => $q->where('category_id', $category->id));
        }

        if ($sort) {
            $query->orderBy('event_date', $sort === 'latest' ? 'desc' : 'asc');
        } else {
            // No sort picked: upcoming first (soonest), then past (newest first)
            $now = now()->toDateTimeString();
            $query->orderByRaw('event_date >= ? desc', [$now])
                ->orderByRaw('CASE WHEN event_date >= ? THEN UNIX_TIMESTAMP(event_date) ELSE -UNIX_TIMESTAMP(event_date) END', [$now]);
        }

        $events = $query->paginate(12)->withQueryString();

        return view('events', compact('events', 'categories', 'selected', 'sort'));
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

        if ($event->event_date->isPast() || $event->status === 'completed') {
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