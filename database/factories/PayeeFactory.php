<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PayeeFactory extends Factory
{
    protected $model = \App\Models\Payee::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'contact' => $this->faker->phoneNumber,
            'payable' => $this->faker->randomFloat(2, 100, 1000),
            'type' => $this->faker->randomElement(['customer', 'supplier']),
            'company_id' => null, // to be set in seeder
        ];
    }
} 