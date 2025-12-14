<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = \App\Models\Order::class;

    public function definition(): array
    {
        return [
            'gross_total' => $this->faker->randomFloat(2, 100, 1000),
            'discount' => $this->faker->randomFloat(2, 0, 100),
            'total_paid' => $this->faker->randomFloat(2, 50, 1000),
            'balance' => $this->faker->randomFloat(2, 0, 500),
            'tyre' => $this->faker->randomElement(['cash', 'credit', 'bank']),
            'customer' => $this->faker->name,
            'number' => $this->faker->phoneNumber,
            'payable' => $this->faker->randomFloat(2, 50, 1000),
            'company_id' => null, // to be set in seeder
        ];
    }
} 