<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyPaymentTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'payment_types' => 'required|array',
            'payment_types.*.id' => 'required|integer:exists:payment_frequencies,id',
            'payment_types.*.should_pay' => 'required|boolean'
        ];
    }
}
