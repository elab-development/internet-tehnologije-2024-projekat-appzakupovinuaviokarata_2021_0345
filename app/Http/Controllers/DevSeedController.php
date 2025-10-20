<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use App\Models\Carrier;
use App\Models\Flight;
use App\Models\Fare;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class DevSeedController extends Controller
{
    public function example(): JsonResponse
    {
       
        try {
            $beg = Airport::firstOrCreate(['iata'=>'BEG'], [
                'name'=>'Nikola Tesla','city'=>'Belgrade','country'=>'Serbia'
            ]);

            $ams = Airport::firstOrCreate(['iata'=>'AMS'], [
                'name'=>'Schiphol','city'=>'Amsterdam','country'=>'Netherlands'
            ]);

            $ju  = Carrier::firstOrCreate(['code'=>'JU'], ['name'=>'Air Serbia']);

            $dep = Carbon::now()->addDays(7)->setTime(8,30);
            $arr = (clone $dep)->addMinutes(150);

            $flight = Flight::firstOrCreate([
                'carrier_id'      => $ju->id,
                'airport_from_id' => $beg->id,
                'airport_to_id'   => $ams->id,
                'flight_no'       => 'JU354',
                'dep_time'        => $dep,
            ],[
                'arr_time'     => $arr,
                'duration_min' => 150,
                'stops'        => 0,
            ]);

            $fare = Fare::firstOrCreate([
                'flight_id'   => $flight->id,
                'cabin_class' => 'ECONOMY',
                'price'       => 129.99,
            ],[
                'currency'        => 'EUR',
                'available_seats' => 5,
                'rules'           => ['baggage'=>'10kg cabin','refund'=>'non-refundable'],
            ]);

            return response()->json([
                'airports' => [$beg, $ams],
                'carrier'  => $ju,
                'flight'   => $flight->load(['origin','destination','carrier']),
                'fare'     => $fare,
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
