<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Rating;
use App\Models\Reservation;
use App\Models\Result;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use App\Support\AdminAlert;

class RatingController extends Controller
{
    public function store(Request $request, Reservation $reservation)
    {
        abort_unless($reservation->user_id === $request->user()->id, 403);

        if ($reservation->status !== 'picked_up') {
            return back()->with('error', 'This reservation cannot be rated.');
        }

        return $this->save($request, ['reservation_id' => $reservation->id]);
    }

    public function storeEvent(Request $request, Event $event)
    {
        $finished = Result::where('event_id', $event->id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if (! $finished) {
            return back()->with('error', 'This event cannot be rated.');
        }

        return $this->save($request, ['event_id' => $event->id]);
    }

    private function save(Request $request, array $target)
    {
        $data = $request->validate([
            'stars' => ['required', 'integer', 'between:1,5'],
        ]);

        try {
            $rating = Rating::firstOrCreate(
                ['user_id' => $request->user()->id] + $target,
                ['stars' => $data['stars']]
            );

            if ($rating->wasRecentlyCreated) {
                AdminAlert::send(
                    'New rating',
                    $request->user()->name . ' gave ' . $data['stars'] . ' ' . ($data['stars'] == 1 ? 'star' : 'stars'),
                    'heroicon-o-star',
                    'warning'
                );
            }
        } catch (UniqueConstraintViolationException $e) {
            // Double click: already saved
        }

        return back()->with('success', 'Thanks for your rating!');
    }
}