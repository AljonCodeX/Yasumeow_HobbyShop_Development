<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Support\AdminAlert;

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

        if (! $sessionId) {
            return response()->json(['message' => 'Ignored']);
        }

        $paid = DB::transaction(function () use ($sessionId) {
        $reservation = Reservation::where('paymongo_session_id', $sessionId)
            ->lockForUpdate()
            ->first();

        // Unknown, or already handled (PayMongo can send the same event twice)
        if (! $reservation || $reservation->status !== 'awaiting_payment') {
            return null;
        }

        $reservation->update([
            'status'      => 'pending',
            'amount_paid' => $reservation->amount_due,
        ]);

        return $reservation;
    });

    if ($paid) {
        AdminAlert::send(
            'Payment received, reservation #' . $paid->id,
            $paid->contact_name . ' paid ₱' . number_format((float) $paid->amount_paid, 2) . ' (QR Ph)',
            'heroicon-o-banknotes',
            'success'
        );

        return response()->json(['message' => 'OK']);
    }

    // Not awaiting payment: maybe the customer paid after the auto-cancel
    $late = Reservation::where('paymongo_session_id', $sessionId)->first();
    $outcome = $late?->recoverLatePayment();

    if ($outcome === 'recovered') {
        AdminAlert::send(
            'Late payment recovered, reservation #' . $late->id,
            $late->contact_name . ' paid after the 30-minute limit. Stock was available, so the reservation is active again.',
            'heroicon-o-banknotes',
            'success'
        );
    } elseif ($outcome === 'refund') {
        AdminAlert::send(
            'Refund needed, reservation #' . $late->id,
            $late->contact_name . ' paid ₱' . number_format((float) $late->amount_due, 2) . ' after the 30-minute limit, but the items are no longer in stock. Refund in PayMongo.',
            'heroicon-o-banknotes',
            'danger'
        );
    }

        return response()->json(['message' => 'OK']);
    }

    private function validSignature(Request $request): bool
    {
        $secret = (string) config('services.paymongo.webhook_secret');

        if ($secret === '') {
            return false;
        }
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
            $secret
        );

        return hash_equals($expected, $received);
    }
}