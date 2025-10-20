<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AirportFactory extends Factory
{
    public function definition(): array
    {
        return [
            'iata'    => strtoupper(fake()->unique()->lexify('???')),
            'name'    => fake()->city().' International',
            'city'    => fake()->city(),
            'country' => fake()->country(),
        ];
    }
}
