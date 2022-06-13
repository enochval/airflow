<?php

namespace App\Helpers;
use App\Exceptions\BreezeException;

class BaseDto
{
    protected array $data;

    public function checkCompulsoryKeysExist(array $compulsoryKeys, array $data)
    {
        foreach ($compulsoryKeys as $key) {
            if (!in_array($key, array_keys($data))) {
                throw new BreezeException(__('general.must_exists'));
            }
        }
    }

    public function extract() : array
    {
        return $this->data;
    }
}
