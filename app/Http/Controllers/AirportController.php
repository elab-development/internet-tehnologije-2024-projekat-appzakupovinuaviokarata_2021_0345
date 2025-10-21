<?php

namespace App\Http\Controllers;


use App\Http\Resources\AirportResource;
use App\Models\Airport;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AirportController extends Controller
{
    public function index(Request $request)
    {
        $q     = (string) $request->query('search', '');
        $limit = 20;
        $key   = 'airports:index:search=' . mb_strtolower($q) . ':limit=' . $limit;
        $ttl   = 60 * 60 * 24; // 24h

        $payload = Cache::remember($key, $ttl, function () use ($q, $limit) {
            $query = Airport::query();

            if ($q !== '') {
                $qq = mb_strtolower($q);
                $query->where(function ($w) use ($qq) {
                    $w->whereRaw('LOWER(iata) = ?', [$qq])              // tačan IATA
                    ->orWhereRaw('LOWER(name) LIKE ?', ["%$qq%"])
                    ->orWhereRaw('LOWER(city) LIKE ?', ["%$qq%"])
                    ->orWhereRaw('LOWER(country) LIKE ?', ["%$qq%"]);
                });
            }

            $items = $query->orderBy('iata')->limit($limit)->get();

            
            return \App\Http\Resources\AirportResource::collection($items)
                ->response()->getData(true);
        });

        return response()->json($payload, 200);
    }
}
