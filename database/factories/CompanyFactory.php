<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'client_id' => Client::factory()->create()->id,
            'name' => $this->faker->company(),
            'email' => $this->faker->companyEmail(),
            'url' => $this->faker->url(),
            'logo' => $this->faker->imageUrl(),
            'phone_no' => $this->faker->phoneNumber(),
            'no_of_employees' => $this->faker->randomNumber(),
            'country_id' => Country::factory()->create()->id,
            'state_id' => State::factory()->create()->id,
            'city' => $this->faker->city(),
            'street' => $this->faker->streetAddress(),
            'postal_code' => $this->faker->postcode(),
            'tax_id' => $this->faker->postcode(),
        ];
    }
}
