<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Flight;

class FareFactory extends Factory
{
    public function definition(): array
    {
        return [
            'flight_id'   => Flight::factory(), //radi ako se pozove samostalno
            'cabin_class' => fake()->randomElement(['ECONOMY','BUSINESS']),
            'price'       => fake()->randomFloat(2, 49, 399),
            'currency'    => 'EUR',
            
        ];
    }
}
