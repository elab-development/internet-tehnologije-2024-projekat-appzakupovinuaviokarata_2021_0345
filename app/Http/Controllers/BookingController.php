<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $r)
    {
        $q = Booking::with(['flight.origin','flight.destination','flight.carrier','fare'])
            ->where('user_id', $r->user()->id);

        if ($s = $r->query('status')) $q->where('status', $s);

        return response()->json($q->paginate($r->integer('per_page',10)));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'flight_id'   => ['required','exists:flights,id'],
            'fare_id'     => ['required','exists:fares,id'],
            'passengers'  => ['required','integer','min:1'],
            'contact'     => ['nullable','array'],
        ]);

        $fare = Fare::findOrFail($data['fare_id']);
        $total = $fare->price * $data['passengers'];

        $booking = Booking::create([
            'user_id'     => $r->user()->id,
            'flight_id'   => $data['flight_id'],
            'fare_id'     => $data['fare_id'],
            'passengers'  => $data['passengers'],
            'status'      => 'pending',
            'total_price' => $total,
            'currency'    => $fare->currency ?? 'EUR',
            'contact'     => $data['contact'] ?? null,
        ]);

        return response()->json($booking->load(['flight.origin','flight.destination','flight.carrier','fare']), 201);
    }

    public function show(Booking $booking, Request $r)
    {
        abort_unless($booking->user_id === $r->user()->id, 403);
        return response()->json($booking->load(['flight.origin','flight.destination','flight.carrier','fare']));
    }

    public function update(Booking $booking, Request $r)
    {
        abort_unless($booking->user_id === $r->user()->id, 403);
        $data = $r->validate([
            'passengers' => ['nullable','integer','min:1'],
            'contact'    => ['nullable','array'],
            'status'     => ['nullable','in:pending,confirmed,cancelled'],
        ]);
        $booking->update($data);
        return response()->json($booking->fresh());
    }

    public function destroy(Booking $booking, Request $r)
    {
        abort_unless($booking->user_id === $r->user()->id, 403);
        $booking->update(['status'=>'cancelled']);
        return response()->json(['message'=>'cancelled']);
    }
}
