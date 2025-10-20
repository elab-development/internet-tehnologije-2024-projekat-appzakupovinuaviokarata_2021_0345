<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use App\Models\Flight;
use App\Http\Resources\FlightResource;
use Illuminate\Http\Request;

class FlightSearchController extends Controller
{
    public function search(Request $request)
    {
        // Validate inputs (date is optional)
        $v = $request->validate([
            'from'      => ['required','string','size:3'],
            'to'        => ['required','string','size:3'],
            'date'      => ['nullable','date'],
            'stops'     => ['nullable','integer','min:0','max:2'],
            'carrier'   => ['nullable','string'],   // comma list e.g. JU,KL
            'cabin'     => ['nullable','string'],   // ECONOMY/BUSINESS...
            'sort'      => ['nullable','string'],   // price_asc, time_asc...
            'page'      => ['nullable','integer','min:1'],
            'per_page'  => ['nullable','integer','min:1','max:50'],
        ]);

        //Resolve IATA -> airport IDs (404 only if FROM/TO invalid)
        $from = Airport::where('iata', strtoupper($v['from']))->first();
        $to   = Airport::where('iata', strtoupper($v['to']))->first();
        if (!$from || !$to) {
            return response()->json(['message' => 'Unknown airport IATA'], 404);
        }

        // Build query
        $q = Flight::with(['origin','destination','carrier','fares'])
            ->where('airport_from_id', $from->id)
            ->where('airport_to_id', $to->id);

        // Apply date filter only if provided
        if (!empty($v['date'])) {
            $q->whereDate('dep_time', $v['date']);
        }

        
        if (isset($v['stops'])) {
            $q->where('stops', $v['stops']);
        }
        if (!empty($v['carrier'])) {
            $codes = collect(explode(',', $v['carrier']))->map(fn($c)=>trim(strtoupper($c)))->filter();
            if ($codes->isNotEmpty()) {
                $q->whereHas('carrier', fn($qq) => $qq->whereIn('code', $codes));
            }
        }
        if (!empty($v['cabin'])) {
            $q->whereHas('fares', fn($qq) => $qq->where('cabin_class', strtoupper($v['cabin'])));
        }

        // Sort (default by dep_time)
        $sort = $v['sort'] ?? 'dep_time_asc';
        match ($sort) {
            'price_asc'  => $q->withMin('fares','price')->orderBy('fares_min_price'),
            'price_desc' => $q->withMin('fares','price')->orderByDesc('fares_min_price'),
            'time_asc'   => $q->orderBy('dep_time'),
            'time_desc'  => $q->orderByDesc('dep_time'),
            default      => $q->orderBy('dep_time'),
        };

        // Return 200 always (even if zero results)
        $perPage = (int)($v['per_page'] ?? 10);
        $results = $q->paginate($perPage);

        return FlightResource::collection($results);
    }

    public function show(Flight $flight)
    {
        $flight->load(['origin','destination','carrier','fares']);
        return new FlightResource($flight);
    }
}
