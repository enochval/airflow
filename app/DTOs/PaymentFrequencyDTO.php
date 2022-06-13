<?php

namespace App\DTOs;

use Illuminate\Support\Arr;

class PaymentFrequencyDTO extends \App\Helpers\BaseDto
{
    public function from(array $data) : self
    {
        $keys = ['payment_type_id', 'company_id', 'frequency', 'should_pay', 'deduction_account_id'];

        $this->checkCompulsoryKeysExist($keys, $data);

        $this->data = Arr::only($data, $keys);

        return $this;
    }
}
