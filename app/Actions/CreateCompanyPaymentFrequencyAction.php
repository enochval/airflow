<?php

namespace App\Actions;

use App\DTOs\PaymentFrequencyDTO;
use App\Models\PaymentFrequency;

class CreateCompanyPaymentFrequencyAction
{
    public function handle(PaymentFrequencyDTO $paymentFrequencyDTO)
    {
        PaymentFrequency::create($paymentFrequencyDTO->extract());
    }
}
