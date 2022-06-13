<?php


namespace App\Actions;

use App\DTOs\PaymentFrequencyDTO;
use App\Enums\PaymentFrequencyEnum;
use App\Enums\PaymentTypesEnum;
use App\Models\Company;
use Illuminate\Support\Arr;
use App\Models\PaymentType;

class CreateCompanyAction
{
    public function handle(array $data)
    {
        $payload = Arr::only($data, [
            'client_id', 'name', 'email', 'url',
            'logo', 'phone_no', 'no_of_employees',
            'country_id', 'state_id', 'city', 'street',
            'postal_code', 'tax_id'
        ]);

        $company = Company::create($payload);

        $this->setUpDefaultCompanyPaymentTypes($company);
    }

    public function setUpDefaultCompanyPaymentTypes(Company $company)
    {
        PaymentType::where('country_id', $company->country_id)->get()->map(function ($paymentType) use ($company) {
            $dto = (new PaymentFrequencyDTO)->from([
                'payment_type_id' => $paymentType->id,
                'company_id' => $company->id,
                'frequency' => PaymentFrequencyEnum::MONTHLY,
                'should_pay' => in_array($paymentType->name, $this->defaultShouldPays()),
                'deduction_account_id' => null
            ]);
            (new CreateCompanyPaymentFrequencyAction)->handle($dto);
        });


    }

    public function defaultShouldPays()
    {
        return [PaymentTypesEnum::NET->value, PaymentTypesEnum::PENSION->value];
    }
}
