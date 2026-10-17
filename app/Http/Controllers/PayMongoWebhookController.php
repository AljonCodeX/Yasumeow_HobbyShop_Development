<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayMongoWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Anyone could post to this URL, so check that PayMongo really sent it
        if (! $this->validSignature($request)) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $event = $request->input('data.attributes', []);

        // Other events: reply 200 so PayMongo doesn't keep retrying
        if (($event['type'] ?? null) !== 'checkout_session.payment.paid') {
            return response()->json(['message' => 'Ignored']);
        }

        $sessionId = $event['data']['id'] ?? null;

        DB::transaction(function () use ($sessionId) {
            $reservation = Reservation::where('paymongo_session_id', $sessionId)
                ->lockForUpdate()
                ->first();

            // Unknown, or already handled (PayMongo can send the same event twice)
            if (! $reservation || $reservation->status !== 'awaiting_payment') {
                return;
            }

            $reservation->update([
                'status'      => 'pending',
                'amount_paid' => $reservation->amount_due,
            ]);
        });

        return response()->json(['message' => 'OK']);
    }

    private function validSignature(Request $request): bool
    {
        $header = $request->header('Paymongo-Signature');

        if (! $header) {
            return false;
        }

        // Header looks like: t=1700000000,te=abc...,li=def...
        $parts = [];
        foreach (explode(',', $header) as $piece) {
            $pair = explode('=', $piece, 2);
            if (count($pair) === 2) {
                $parts[trim($pair[0])] = trim($pair[1]);
            }
        }

        $timestamp = $parts['t'] ?? '';
        $livemode = (bool) $request->input('data.attributes.livemode');

        // te = test mode signature, li = live mode signature
        $received = $livemode ? ($parts['li'] ?? '') : ($parts['te'] ?? '');

        $expected = hash_hmac(
            'sha256',
            $timestamp . '.' . $request->getContent(),
            (string) config('services.paymongo.webhook_secret')
        );

        return hash_equals($expected, $received);
    }
}