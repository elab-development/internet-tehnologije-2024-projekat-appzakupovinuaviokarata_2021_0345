<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CarrierFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->lexify('??')),
            'name' => fake()->company().' Air',
        ];
    }
}
