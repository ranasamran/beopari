<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BankTransFactory extends Factory
{
    protected $model = \App\Models\BankTrans::class;

    public function definition(): array
    {
        return [
            'bank_id' => null, // to be set in seeder
            'name' => $this->faker->word,
            'cus_id' => null, // to be set in seeder
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'status' => $this->faker->randomElement([0, 1]),
            'datetime' => $this->faker->dateTimeThisYear,
            'description' => $this->faker->sentence,
        ];
    }
} 