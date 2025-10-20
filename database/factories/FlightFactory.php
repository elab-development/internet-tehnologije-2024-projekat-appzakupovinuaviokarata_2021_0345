<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Airport;
use App\Models\Carrier;

class FlightFactory extends Factory
{
    public function definition(): array
    {
        $dep = fake()->dateTimeBetween('+1 day', '+30 days');
        $dur = fake()->numberBetween(45, 240); // min
        $arr = (clone $dep)->modify("+{$dur} minutes");

        // različiti aerodromi itd
        $origin = Airport::inRandomOrder()->first() ?? Airport::factory()->create();
        do {
            $dest = Airport::inRandomOrder()->first() ?? Airport::factory()->create();
        } while ($dest->id === $origin->id);

        return [
            'flight_no'        => strtoupper(fake()->bothify('??###')),
            'airport_from_id'  => $origin->id,
            'airport_to_id'    => $dest->id,
            'carrier_id'       => Carrier::inRandomOrder()->first()->id ?? Carrier::factory()->create()->id,
            'dep_time'         => $dep,
            'arr_time'         => $arr,
            'duration_min'     => $dur,
            'stops'            => fake()->randomElement([0,1]),
            'seats_total'      => fake()->numberBetween(80, 220),
            'status'           => 'SCHEDULED',
        ];
    }
}
