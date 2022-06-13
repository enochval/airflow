<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;

class OnboardRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users',
            'phone_no' => 'required|numeric|unique:users',
            'password' => 'required|string|min: 8',

            'company' => 'required|array',
            'company.name' => 'required|string|unique:companies,name',
            'company.url' => 'required|url|unique:companies,url',
            'company.logo' => 'nullable|string',
            'company.email' => 'nullable|email|unique:companies,email',
            'company.phone_no' => 'required|numeric|unique:companies,phone_no',
            'company.no_of_employees' => 'nullable|numeric',
            'company.country' => 'required|string',
            'company.state' => 'required|string',
            'company.street' => 'required|string',
            'company.city' => 'nullable|string',
            'company.postal_code' => 'nullable|string',
            'company.tax_id' => 'nullable|string',
        ];
    }
}
