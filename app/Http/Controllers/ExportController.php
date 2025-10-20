<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use App\Models\Booking;
use App\Models\Flight;
use App\Http\Resources\FlightResource;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Str;

class ExportController extends Controller
{
    // ================= CSV: FLIGHTS =================
    public function flightsCsv(Request $request)
    {
        $v = $request->validate([
            'from'    => ['required','string','size:3'],
            'to'      => ['required','string','size:3'],
            'date'    => ['nullable','date'],
            'stops'   => ['nullable','integer','min:0','max:2'],
            'carrier' => ['nullable','string'],
            'cabin'   => ['nullable','string'],
            'sort'    => ['nullable','string'],
        ]);

        $from = \App\Models\Airport::where('iata', strtoupper($v['from']))->first();
        $to   = \App\Models\Airport::where('iata', strtoupper($v['to']))->first();
        if (!$from || !$to) {
            return response()->json(['message' => 'Unknown airport IATA'], 404);
        }

        $q = \App\Models\Flight::with([
                'origin','destination','carrier',
                'fares' => fn($qq) => $qq->orderBy('price')
            ])
            ->where('airport_from_id', $from->id)
            ->where('airport_to_id', $to->id);

        if (!empty($v['date']))  $q->whereDate('dep_time', $v['date']);
        if (isset($v['stops']))  $q->where('stops', $v['stops']);
        if (!empty($v['carrier'])) {
            $codes = collect(explode(',', $v['carrier']))->map(fn($c)=>trim(strtoupper($c)))->filter();
            if ($codes->isNotEmpty()) $q->whereHas('carrier', fn($qq)=>$qq->whereIn('code',$codes));
        }
        if (!empty($v['cabin'])) {
            $q->whereHas('fares', fn($qq)=>$qq->where('cabin_class', strtoupper($v['cabin'])));
        }

        match ($v['sort'] ?? 'dep_time_asc') {
            'price_asc'  => $q->withMin('fares','price')->orderBy('fares_min_price'),
            'price_desc' => $q->withMin('fares','price')->orderByDesc('fares_min_price'),
            'time_desc'  => $q->orderByDesc('dep_time'),
            default      => $q->orderBy('dep_time'),
        };

        // napravi CSV u stringu (jednostavno i stabilno)
        $rows = [];
        $rows[] = ['Flight','From','To','Carrier','Departs','Arrives','Stops','Cheapest EUR'];

        foreach ($q->get() as $f) {
            $cheapest = optional($f->fares->first())->price;
            $rows[] = [
                $f->flight_no,
                $f->origin?->iata,
                $f->destination?->iata,
                $f->carrier?->code,
                optional($f->dep_time)->format('Y-m-d H:i'),
                optional($f->arr_time)->format('Y-m-d H:i'),
                $f->stops,
                $cheapest,
            ];
        }

        // ručno generiši CSV
        $csv = '';
        $fh = fopen('php://temp', 'r+');
        foreach ($rows as $r) { fputcsv($fh, $r); }
        rewind($fh);
        $csv = stream_get_contents($fh);
        fclose($fh);

        $filename = 'flights_' . strtoupper($v['from'].'_'.$v['to']).'.csv';
        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control'       => 'no-cache, private',
        ]);
    }


    // ================= CSV: MY BOOKINGS =================
    public function bookingsCsv(Request $request)
    {
        $user = $request->user();

        $filename = 'bookings_'.$user->id.'.csv';

        $response = new StreamedResponse(function () use ($user) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['BookingID','Status','Flight','From','To','Carrier','Departs','Arrives','Fare EUR','Created']);

            $user->bookings()
                ->with(['flight.origin','flight.destination','flight.carrier','fare'])
                ->latest()
                ->chunk(500, function ($rows) use ($out) {
                    foreach ($rows as $b) {
                        fputcsv($out, [
                            $b->id,
                            $b->status,
                            $b->flight?->flight_no,
                            $b->flight?->origin?->iata,
                            $b->flight?->destination?->iata,
                            $b->flight?->carrier?->code,
                            optional($b->flight?->dep_time)->format('Y-m-d H:i'),
                            optional($b->flight?->arr_time)->format('Y-m-d H:i'),
                            $b->fare?->price,
                            $b->created_at?->toDateTimeString(),
                        ]);
                    }
                });

            fclose($out);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'"');

        return $response;
    }

    // ================= ICS: SINGLE BOOKING =================
    public function bookingIcs(Request $request, Booking $booking)
    {
        // zaštita: korisnik može samo svoj booking
        if ($booking->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Zabranjen pristup.'], 403);
        }

        $booking->load(['flight.origin','flight.destination','flight.carrier','fare']);
        $f = $booking->flight;

        $uid = 'booking-'.$booking->id.'@'.parse_url(config('app.url') ?? 'localhost', PHP_URL_HOST);
        $dtStart = $f->dep_time->clone()->utc()->format('Ymd\THis\Z');
        $dtEnd   = $f->arr_time->clone()->utc()->format('Ymd\THis\Z');
        $summary = sprintf('%s %s→%s (%s)',
            $f->flight_no, $f->origin?->iata, $f->destination?->iata, $f->carrier?->code
        );
        $location = sprintf('%s (%s) → %s (%s)',
            $f->origin?->name, $f->origin?->iata, $f->destination?->name, $f->destination?->iata
        );

        $ics = "BEGIN:VCALENDAR\r\n".
               "VERSION:2.0\r\n".
               "PRODID:-//FlightTickets//EN\r\n".
               "BEGIN:VEVENT\r\n".
               "UID:{$uid}\r\n".
               "DTSTAMP:".now()->utc()->format('Ymd\THis\Z')."\r\n".
               "DTSTART:{$dtStart}\r\n".
               "DTEND:{$dtEnd}\r\n".
               "SUMMARY:".self::escapeIcs($summary)."\r\n".
               "LOCATION:".self::escapeIcs($location)."\r\n".
               "DESCRIPTION:Booking #{$booking->id}; Fare EUR ".($booking->fare?->price)."\r\n".
               "END:VEVENT\r\n".
               "END:VCALENDAR\r\n";

        return response($ics, 200, [
            'Content-Type'        => 'text/calendar; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="booking_'.$booking->id.'.ics"',
        ]);
    }

    private static function escapeIcs(string $text): string
    {
        return str_replace(
            ["\\", ";", ",", "\n", "\r"],
            ["\\\\","\\;","\\,","\\n",""],
            $text
        );
    }

    // ================= PDF: BOOKING/TICKET =================
    public function bookingPdf(Request $request, Booking $booking)
    {
        if ($booking->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Zabranjen pristup.'], 403);
        }

        
        $booking->load(['flight.origin','flight.destination','flight.carrier','fare','user']);
        $pdf = \PDF::loadView('pdf.booking', ['b' => $booking]);

        $filename = 'booking_'.$booking->id.'.pdf';
        return $pdf->download($filename); 
    }
}
