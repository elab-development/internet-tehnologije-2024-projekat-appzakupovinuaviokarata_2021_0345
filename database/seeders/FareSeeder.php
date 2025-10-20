<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Flight;
use App\Models\Fare;

class FareSeeder extends Seeder
{
    public function run(): void
    {
        Flight::all()->each(function (Flight $flight) {
            // Obavezno ECONOMY
            Fare::factory()->for($flight)->create([
                'cabin_class' => 'ECONOMY',
            ]);

            // Ponekad i BUSINESS
            if (rand(0, 1)) {
                Fare::factory()->for($flight)->create([
                    'cabin_class' => 'BUSINESS',
                ]);
            }
        });
    }
}