<?php

namespace Database\Factories;

use App\Enums\AccountTypesEnum;
use App\Models\Bank;
use App\Models\Company;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'company_id' => Company::factory()->create()->id,
            'bank_id' => Bank::factory()->create()->id,
            'currency_id' => Currency::factory()->create()->id,
            'bank_code' => $this->faker->numberBetween(100, 300),
            'account_name' => $this->faker->company(),
            'account_no' => $this->faker->numberBetween(1000000000, 9999999999),
            'account_type' => $this->faker->randomElement(['virtual_account']),
            'account_reference' => $this->faker->numberBetween(1000000000000000, 9999999999999999),
            'is_active' => $this->faker->boolean(100),
        ];
    }
}
