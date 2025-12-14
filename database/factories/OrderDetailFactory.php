<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderDetailFactory extends Factory
{
    protected $model = \App\Models\OrderDetail::class;

    public function definition(): array
    {
        return [
            'order_id' => null, // to be set in seeder
            'product_id' => null, // to be set in seeder
            'name' => $this->faker->word,
            'quantity' => $this->faker->numberBetween(1, 10),
            'price' => $this->faker->randomFloat(2, 10, 200),
            'created_at' => $this->faker->dateTimeThisYear,
        ];
    }
} 