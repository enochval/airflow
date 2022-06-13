<?php


namespace App\Http\Requests;

use App\Exceptions\BreezeValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class BaseFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator): BreezeValidationException
    {
        throw new BreezeValidationException(errors: $validator->errors()->toArray());
    }
}
