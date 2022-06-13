<?php

namespace Database\Factories;

use App\Enums\PaymentFrequencyEnum;
use App\Models\Company;
use App\Models\PaymentType;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFrequencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'payment_type_id' => PaymentType::factory()->create()->id,
            'company_id' => Company::factory()->create()->id,
            'frequency' => PaymentFrequencyEnum::MONTHLY,
            'should_pay' => true,
        ];
    }
}
