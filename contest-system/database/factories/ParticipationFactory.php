<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Contest;

class ParticipationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'contest_id' => Contest::first()->id ?? Contest::factory(),
            'status' => 'submitted',
            'answers' => [],
            'score' => $this->faker->numberBetween(0, 10),
            'submitted_at' => $this->faker->dateTimeBetween('-2 days', 'now'),
        ];
    }
}