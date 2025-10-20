<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use App\Models\Flight;
use App\Http\Resources\FlightResource;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Cache;

class FlightSearchController extends Controller
{
    public function search(Request $request)
    {
        $v = $request->validate([
            'from'     => ['required','string','size:3'],
            'to'       => ['required','string','size:3'],
            'date'     => ['nullable','date'],
            'stops'    => ['nullable','integer','min:0','max:2'],
            'carrier'  => ['nullable','string'],
            'cabin'    => ['nullable','string'],
            'sort'     => ['nullable','string'],
            'page'     => ['nullable','integer','min:1'],
            'per_page' => ['nullable','integer','min:1','max:50'],
        ]);

        $from = Airport::where('iata', strtoupper($v['from']))->first();
        $to   = Airport::where('iata', strtoupper($v['to']))->first();
        if (!$from || !$to) {
            return response()->json(['message' => 'Unknown airport IATA'], 404);
        }

        // Keš ključ na osnovu parametara
        $keyBase = [
            'from'     => strtoupper($v['from']),
            'to'       => strtoupper($v['to']),
            'date'     => $v['date'] ?? null,
            'stops'    => $v['stops'] ?? null,
            'carrier'  => $v['carrier'] ?? null,
            'cabin'    => $v['cabin'] ?? null,
            'sort'     => $v['sort'] ?? 'dep_time_asc',
            'page'     => (int) $request->query('page', 1),
            'per_page' => (int) ($v['per_page'] ?? 10),
        ];
        $cacheKey = 'flights:search:' . sha1(json_encode($keyBase));
        $ttl = 60 * 5; // 5 min

        // Sve “skupo” desi se unutar remember — DB se pogađa samo na cache-miss
        $payload = Cache::remember($cacheKey, $ttl, function () use ($from, $to, $v, $request) {
            $q = Flight::with(['origin','destination','carrier','fares'])
                ->where('airport_from_id', $from->id)
                ->where('airport_to_id', $to->id);

            if (!empty($v['date']))      $q->whereDate('dep_time', $v['date']);
            if (isset($v['stops']))      $q->where('stops', $v['stops']);
            if (!empty($v['carrier'])) {
                $codes = collect(explode(',', $v['carrier']))->map(fn($c)=>trim(strtoupper($c)))->filter();
                if ($codes->isNotEmpty()) $q->whereHas('carrier', fn($qq) => $qq->whereIn('code', $codes));
            }
            if (!empty($v['cabin']))     $q->whereHas('fares', fn($qq) => $qq->where('cabin_class', strtoupper($v['cabin'])));

            $sort = $v['sort'] ?? 'dep_time_asc';
            match ($sort) {
                'price_asc'  => $q->withMin('fares','price')->orderBy('fares_min_price'),
                'price_desc' => $q->withMin('fares','price')->orderByDesc('fares_min_price'),
                'time_asc'   => $q->orderBy('dep_time'),
                'time_desc'  => $q->orderByDesc('dep_time'),
                default      => $q->orderBy('dep_time'),
            };

            $perPage = (int) ($v['per_page'] ?? 10);
            $results = $q->paginate($perPage)->appends($request->query());

            // Keširamo gotov payload (data/meta/links) iz Resource kolekcije
            return (FlightResource::collection($results))
                ->response()
                ->getData(true);
        });

        // Vrati baš keširani payload
        return response()->json($payload, 200);
    }

    public function show(Flight $flight)
    {
        $flight->load(['origin','destination','carrier','fares']);
        return new FlightResource($flight);
    }
}
