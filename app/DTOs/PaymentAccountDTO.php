<?php

namespace App\DTOs;

use App\Helpers\BaseDto;
use Illuminate\Support\Arr;

class PaymentAccountDTO extends BaseDto
{
    public function from(array $data) : self
    {
        $keys = [
            'company_id',
            'bank_id',
            'currency_id',
            'bank_code',
            'account_name',
            'account_no',
            'account_type',
            'account_reference',
            'is_active',
        ];

        $this->checkCompulsoryKeysExist($keys, $data);

        $this->data = Arr::only($data, $keys);

        return $this;
    }
}
