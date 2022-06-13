<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CurrencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->country(),
            'code' => $this->faker->currencyCode(),
            'symbol' => $this->faker->currencyCode(),
            'decimal_digits' => $this->faker->randomNumber(),
            'rounding' => $this->faker->randomNumber(),
        ];
    }
}
