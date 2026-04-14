<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\TravelOrderModel;
use App\Models\User;

class TravelOrderModelFactory extends Factory
{
    protected $model = TravelOrderModel::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'destination' => $this->faker->city(),
            'departure_date' => $this->faker->date(),
            'return_date' => $this->faker->date(),
            'status' => 'requested',
        ];
    }
}
