<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PublicDataController extends Controller
{
    public function weatherSummary(Request $request)
    {
        $v = $request->validate([
            'from' => ['required','string','size:3'],
            'to'   => ['required','string','size:3'],
            'date' => ['required','date'], // tražimo obavezno da bi bilo “obrađeno” po danu
        ]);

        $from = Airport::where('iata', strtoupper($v['from']))->first();
        $to   = Airport::where('iata', strtoupper($v['to']))->first();
        if (!$from || !$to) {
            return response()->json(['message' => 'Unknown airport IATA'], 404);
        }

        // cache key po parametrima
        $cacheKey = 'wx:summary:' . sha1(json_encode([
            'from'=>$from->id,'to'=>$to->id,'date'=>$v['date']
        ]));

        return Cache::remember($cacheKey, 60 * 30, function () use ($from, $to, $v) {
            try {
                $dep = $this->fetchDailyWeather($from->city, $from->country, $v['date']);
                $arr = $this->fetchDailyWeather($to->city,   $to->country,   $v['date']);

                return response()->json([
                    'from' => [
                        'iata' => $from->iata,
                        'city' => $from->city,
                        'country' => $from->country,
                        'date' => $v['date'],
                        'forecast' => $dep, // { tmin, tmax, wind_kmh, precip_mm, summary }
                    ],
                    'to' => [
                        'iata' => $to->iata,
                        'city' => $to->city,
                        'country' => $to->country,
                        'date' => $v['date'],
                        'forecast' => $arr,
                    ],
                ], 200);
            } catch (\Throwable $e) {
                // uniformna greška pri eksternim pozivima
                return response()->json([
                    'message' => 'External weather service unavailable',
                    'errors'  => ['detail' => $e->getMessage()],
                ], 502);
            }
        });
    }

    /**
     * Dohvata prognozu za jedan grad/državu i dan preko Open-Meteo geocoding + forecast.
     * Vraća tmin, tmax, wind_kmh, precip_mm, summary
     */
    private function fetchDailyWeather(string $city, ?string $country, string $date): array
    {
        // Geocoding (grad -> lat/lon)
        $geo = Http::timeout(8)->get('https://geocoding-api.open-meteo.com/v1/search', [
            'name'     => $city,
            'count'    => 1,
            'language' => 'en',
            'format'   => 'json',
        ])->throw()->json();

        if (empty($geo['results'][0])) {
            throw new \RuntimeException("Geocoding failed for {$city}");
        }

        $lat = $geo['results'][0]['latitude'];
        $lon = $geo['results'][0]['longitude'];

        // Forecast (dnevni sažetak)
        $d = Carbon::parse($date)->toDateString();
        $wx = Http::timeout(8)->get('https://api.open-meteo.com/v1/forecast', [
            'latitude'  => $lat,
            'longitude' => $lon,
            'daily'     => 'temperature_2m_max,temperature_2m_min,precipitation_sum,windspeed_10m_max',
            'timezone'  => 'UTC',
            'start_date'=> $d,
            'end_date'  => $d,
        ])->throw()->json();

        $daily = $wx['daily'] ?? [];
        // Ako API ne vrati ništa za taj dan:
        if (empty($daily) || empty($daily['time'][0] ?? null)) {
            throw new \RuntimeException("No forecast for {$city} on {$d}");
        }

        $tmin = ($daily['temperature_2m_min'][0] ?? null);
        $tmax = ($daily['temperature_2m_max'][0] ?? null);
        $prec = ($daily['precipitation_sum'][0]  ?? null);
        $wind = ($daily['windspeed_10m_max'][0]  ?? null);

        // “obrađeni” kratki opis:
        $summary = $this->summarize($tmin, $tmax, $prec, $wind);

        return [
            'tmin'       => $tmin,
            'tmax'       => $tmax,
            'wind_kmh'   => $wind,
            'precip_mm'  => $prec,
            'summary'    => $summary,
        ];
    }

    private function summarize($tmin, $tmax, $prec, $wind = null): string
    {
        $parts = [];
        if ($tmin !== null && $tmax !== null) $parts[] = "{$tmin}–{$tmax}°C";
        if ($prec !== null) $parts[] = $prec > 0 ? "{$prec} mm rain" : 'no rain';
        if ($wind !== null) $parts[] = "{$wind} km/h wind";
        return implode(', ', $parts);
    }
}
