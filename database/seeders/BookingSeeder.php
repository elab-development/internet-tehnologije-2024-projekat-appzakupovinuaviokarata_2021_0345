<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Flight;
use App\Models\Fare;
use App\Models\Booking;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        // za svakog usera 1-3 rezervacije
        User::all()->each(function ($u) {
            $count = rand(1, 3);

            for ($i = 0; $i < $count; $i++) {
                $flight = Flight::inRandomOrder()->first();
                if (!$flight) continue;

                // izaberi postojeći fare tog leta ili ga kreiraj
                $fare = $flight->fares()->inRandomOrder()->first();
                if (!$fare) {
                    $fare = Fare::factory()->for($flight)->create();
                }

                Booking::create([
                    'user_id'   => $u->id,
                    'flight_id' => $flight->id,
                    'fare_id'   => $fare->id,              
                    'status'    => fake()->randomElement(['PENDING','CONFIRMED','CANCELLED']),
                    'total_price' => $fare->price,                
                    'currency'    => $fare->currency ?? 'EUR',
                ]);
            }
        });
    }
}