<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Flight;
use App\Models\Airport;
use App\Models\Carrier;

class FlightSeeder extends Seeder
{
    public function run(): void
    {
        // Uveri se da ih ima ako da onda idemo dalje
        if (Airport::count() < 2) return;
        if (Carrier::count() < 1) return;

        \App\Models\Flight::factory()->count(30)->create();
    }
}
