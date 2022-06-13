<?php

namespace App\Actions;

use App\DTOs\PaymentFrequencyDTO;
use App\Models\PaymentFrequency;

class UpdateCompanyPaymentFrequencyAction
{
    public function handle(PaymentFrequency $paymentFrequency, PaymentFrequencyDTO $paymentFrequencyDTO)
    {
        $paymentFrequency->update($paymentFrequencyDTO->extract());
    }
}
