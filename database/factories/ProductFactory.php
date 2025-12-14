<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = \App\Models\Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'quantity' => $this->faker->numberBetween(10, 100),
            'cost_price' => $this->faker->randomFloat(2, 10, 100),
            'retail_price' => $this->faker->randomFloat(2, 20, 200),
            'margin' => $this->faker->randomFloat(2, 5, 50),
            'description' => $this->faker->sentence,
            'company_id' => null, // to be set in seeder
        ];
    }
} 