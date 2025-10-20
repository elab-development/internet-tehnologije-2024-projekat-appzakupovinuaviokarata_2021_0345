<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Flight;

class BookingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'   => User::factory(),
            'flight_id' => Flight::factory(),
            'status'    => fake()->randomElement(['PENDING','CONFIRMED','CANCELLED']),
        ];
    }
}
