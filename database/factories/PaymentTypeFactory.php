<?php

namespace Database\Factories;

use App\Enums\PaymentFrequencyEnum;
use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $paymentFrequencies = PaymentFrequencyEnum::valueArray();
        $total = count($paymentFrequencies);

        return [
            'country_id' => Country::factory()->create()->id,
            'name' => $paymentFrequencies[random_int(0, ($total-1))]
        ];
    }
}
