<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PayeeTransFactory extends Factory
{
    protected $model = \App\Models\PayeeTrans::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'cus_id' => null, // to be set in seeder
            'amount' => $this->faker->randomFloat(2, 10, 500),
            'remain_amount' => $this->faker->randomFloat(2, 0, 500),
            'status' => $this->faker->randomElement([0, 1]),
            'datetime' => $this->faker->dateTimeThisYear,
            'description' => $this->faker->sentence,
        ];
    }
} 