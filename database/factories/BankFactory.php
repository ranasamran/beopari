<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BankFactory extends Factory
{
    protected $model = \App\Models\Bank::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->company,
            'number' => $this->faker->bankAccountNumber,
            'name' => $this->faker->name,
            'balance' => $this->faker->randomFloat(2, 1000, 10000),
            'status' => $this->faker->randomElement([0, 1]),
            'company_id' => null, // to be set in seeder
        ];
    }
} 