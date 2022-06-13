<?php

namespace App\Actions\Account;

use App\DTOs\BankDTO;
use App\DTOs\PaymentAccountDTO;
use App\Enums\AccountTypesEnum;
use App\Enums\EnvironmentEnum;
use App\Enums\ProvidersEnum;
use App\Exceptions\BreezeException;
use App\Helpers\BreezeRequest;
use App\Models\Company;
use App\Models\PaymentAccount;
use Carbon\Carbon;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\Bank;

class CreateVirtualAccountAction
{
    const REF_PREFIX = 'SEAMLESSHR';

    public function handle(Company $company): void
    {
        if (!app()->environment(EnvironmentEnum::PRODUCTION->value)) {

            $data = $this->mockVirtualAccountCreateAPI($company);

        } else {

            $data = $this->virtualAccountCreateAPI($company);
        }

        if (!$data) {
            throw new BreezeException(__('general.try_again', ['extra' => 'Unable to create virtual account,']));
        }

        $bank_name = Arr::get($data, 'bank_name');

        (new CreateBankAction())->handle((new BankDTO())->from([
            'name' => $bank_name,
            'code' => Arr::get($data, 'bank_code')
        ]));

        $bank = Bank::where('name', $bank_name)->first();

        (new CreatePaymentAccountAction())->handle((new PaymentAccountDTO())->from([
            'company_id' => $company->id,
            'bank_id' => $bank->id,
            'currency_id' => Arr::get($data, 'currency_id', 78), // How do we link currency to company or countries...
            'bank_code' => $bank->code,
            'account_name' => Arr::get($data, 'account_name'),
            'account_no' => Arr::get($data, 'nuban'),
            'account_type' => AccountTypesEnum::VIRTUAL_ACCOUNT->value,
            'account_reference' => Arr::get($data, 'account_reference'),
            'is_active' => true,
        ]));
    }

    private function generateAccountReference(): string
    {
        return self::REF_PREFIX . time();
    }

    private function virtualAccountCreateAPI(Company $company): ?array
    {
        $request = [
            'account_name' => $company->name,
            'account_reference' => $this->generateAccountReference(),
            'email' => $company->email,
            'mobilenumber' => $company->phone_no,
            'country' => Str::limit($company->country->country_code, 2, null),
            'meta' => [
                'staff_strength' => $company->no_of_employees,
                'payroll_schedule' => config('providers.flutterwave.payroll_schedule')
            ],
        ];

        $url = config('providers.flutterwave.base_url') . '/v3/payout-subaccounts';

        $curl = BreezeRequest::post($url, $request, [
            "Authorization" => "Bearer " . config('providers.flutterwave.token')
        ], ProvidersEnum::FLUTTERWAVE->value);

        if (
            Arr::has($curl, 'status') &&
            Arr::get($curl, 'status') > 202
        ) {
            throw new BreezeException(__('general.try_again', ['extra' => 'Unable to create virtual account,']));
        }

        return Arr::get($curl, 'data.data');
    }

    private function mockVirtualAccountCreateAPI(Company $company): array
    {
        $faker = Factory::create();
        return [
            'id' => $faker->uuid(),
            'account_reference' => $this->generateAccountReference(),
            'email' => $company->email,
            'mobilenumber' => $company->phone_no,
            'country' => Str::limit($company->country->country_code, 2, null),
            'bank_name' => 'HighStreet MFB bank',
            'bank_code' => '035',
            'status' => $faker->boolean(100),
            'nuban' => $this->generationNuban($faker),
            'account_name' => $company->name,
            'barter_id' => $faker->numberBetween(100000000000000, 999999999999999),
            "meta" => ["staff_strength" => $company->no_of_employees, "payroll_schedule" => "monthly"],
            "created_at" => Carbon::now()->toDayDateTimeString()
        ];
    }

    private function generationNuban(Generator $faker): int
    {
        $nuban = $faker->numberBetween(1000000000, 9999999999);

        while(PaymentAccount::where('account_no', $nuban)->exists()) {
            $nuban = $faker->numberBetween(1000000000, 9999999999);
        }

        return $nuban;
    }
}
