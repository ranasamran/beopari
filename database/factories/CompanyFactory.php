<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = \App\Models\Company::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'contact' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'logo' => $this->faker->imageUrl(200, 200, 'business'),
            'shopname' => $this->faker->companySuffix,
        ];
    }
} 