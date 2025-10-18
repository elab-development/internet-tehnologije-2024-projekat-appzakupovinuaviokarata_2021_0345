<?php

namespace App\Http\Controllers;

use App\Http\Resources\AirportResource;
use App\Models\Airport;

use Illuminate\Http\Request;

class AirportController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('search');
        $query = Airport::query();

        if ($q) {
            $q = mb_strtolower($q);
            $query->where(function($w) use ($q) {
                $w->whereRaw('LOWER(iata) = ?', [$q])
                  ->orWhereRaw('LOWER(name) LIKE ?', ["%$q%"])
                  ->orWhereRaw('LOWER(city) LIKE ?', ["%$q%"])
                  ->orWhereRaw('LOWER(country) LIKE ?', ["%$q%"]);
            });
        }

        return AirportResource::collection($query->limit(20)->get());
    }
}
