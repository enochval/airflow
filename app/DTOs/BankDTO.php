<?php

namespace App\DTOs;

use App\Helpers\BaseDto;
use Illuminate\Support\Arr;

class BankDTO extends BaseDto
{
    public function from(array $data) : self
    {
        $keys = [
            'name',
            'code'
        ];

        $this->checkCompulsoryKeysExist($keys, $data);

        $this->data = Arr::only($data, $keys);

        return $this;
    }
}
