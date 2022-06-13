<?php


namespace App\Services;

use App\Actions\Account\CreateVirtualAccountAction;
use App\Actions\CreateClientAction;
use App\Actions\CreateCompanyAction;
use App\Actions\CreateUserAction;
use App\Actions\UpdateCompanyPaymentFrequencyAction;
use App\DTOs\PaymentFrequencyDTO;
use App\Enums\OnboardingSteps;
use App\Exceptions\BreezeException;
use App\Exceptions\BreezeNotFoundException;
use App\Helpers\AuditLogger;
use App\Models\Client;
use App\Models\Company;
use App\Models\Country;
use App\Models\PaymentFrequency;
use App\Models\State;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ConfigService
{
    public function onboardCompany(array $data): void
    {
        $email = Arr::get($data, 'email');
        $company_name = Arr::get($data, 'company.name');
        $company_url = Arr::get($data, 'company.url');
        $country_name = Arr::get($data, 'company.country');
        $state_name = Arr::get($data, 'company.state');

        $country = $this->findCountryByName($country_name);

        if (!$country) {
            throw new BreezeNotFoundException(__('general.not_found', [
                'model' => 'Country'
            ]));
        }

        $state = $this->findStateByName($country->id, $state_name);

        if (!$state) {
            throw new BreezeNotFoundException(__('general.not_found', [
                'model' => 'State'
            ]));
        }

        $data['company']['country_id'] = $country->id;
        $data['company']['state_id'] = $state->id;

        DB::beginTransaction();

        (new CreateClientAction())->handle($company_name, $company_url);

        $client = $this->findClientByName($company_name);

        if (!$client) {
            DB::rollBack();
            throw new BreezeException(__('general.try_again', [
                'extra' => 'Unable to create client'
            ]));
        }

        $data['company']['client_id'] = $client->id;

        (new CreateCompanyAction())->handle($data['company']);


        $company = $this->findCompanyName($company_name);

        if (!$company) {
            DB::rollBack();
            throw new BreezeException(__('general.try_again', [
                'extra' => 'Unable to create company'
            ]));
        }


        $data['company_id'] = $company->id;

        (new CreateUserAction())->handle($data);

        $user = $this->findUserByEmail($email);

        if (!$user) {
            DB::rollBack();
            throw new BreezeException(__('general.try_again', [
                'extra' => 'Unable to create user'
            ]));
        }

        DB::commit();

        // Send notification to user, but I guess the onbaording
        // team will handle this aspect... if not, it will happen here

        (new AuditLogger($user->id, __('audits.onboard_company')))->logAsUpdated();
    }

    public function findClientByName(string $name): ?Client
    {
        return Client::whereName($name)->first();
    }

    public function findCompanyName(string $name): ?Company
    {
        return Company::whereName($name)->first();
    }

    public function findUserByEmail(string $email): ?User
    {
        return User::whereEmail($email)->first();
    }

    public function findCountryByName(string $country_name): ?Country
    {
        return Country::whereName($country_name)->first();
    }

    public function findStateByName(int $country_id, string $state_name): ?State
    {
        return State::where('country_id', $country_id)
            ->where('name', $state_name)
            ->first();
    }

    public function getClientId(string $url): ?string
    {
        $client = Client::where('url', $url)->first();

        return $client ? base64_encode($client->id) : null;
    }

    public function setupVirtualAccount(Company $company, ?User $user = null): array
    {
        // check if virtual account already exist for this company
        if ($company->virtualAccount()->exists()) {
            throw new BreezeException(__('general.exists', ['entity' => 'Virtual account']));
        }

        (new CreateVirtualAccountAction())->handle($company);

        if (!$company->virtualAccount()->exists()) {
            throw new BreezeException(__('general.action_failed'));
        }

        // update the onboarding step
        $company->client()->update([
            'onboarding_step' => OnboardingSteps::SETUP_DEDUCTIONS->value
        ]);


        if($user) {
            (new AuditLogger($user->id, __('audits.create_virtual_account', [
                'user' => $user->last_name
            ])))->logAsCreated();
        }

        $virtual_account = $company->virtualAccount()->with(['bank', 'currency'])->first();

        return [
            'account_number' => $virtual_account->account_no,
            'account_name' => $virtual_account->account_name,
            'bank_name' => $virtual_account->bank->name,
        ];
    }

    public function listPaymentTypes(Company $company): array
    {
        $companyPaymentFrequencies = PaymentFrequency::with('paymentType')
            ->where('company_id', $company->id)
            ->get();

        if ($companyPaymentFrequencies->isEmpty()) {
            (new CreateCompanyAction)->setUpDefaultCompanyPaymentTypes($company);

            $companyPaymentFrequencies = PaymentFrequency::with('paymentType')
                ->where('company_id', $company->id)
                ->get();
        }
        return $companyPaymentFrequencies->transform(function ($frequency) {
            return $this->paymentFrequenciesSchema($frequency);
        })->toArray();
    }

    public function paymentFrequenciesSchema(PaymentFrequency $frequency): array
    {
        return [
            'id' => $frequency->id,
            'payment_item' => $frequency->paymentType->name,
            'frequency' => $frequency->frequency,
            'should_pay' => $frequency->should_pay
        ];
    }

    public function updatePaymentFrequencies(array $data, bool $updateStep = false, ?User $user = null): bool
    {
        $frequencies = PaymentFrequency::find(collect($data)->pluck('id')->toArray());
        $canUpdate = false;
        foreach ($data as $datum) {
            $frequency = $frequencies->where('id', $datum['id'])->first();

            if ($frequency) {
                $dto = (new PaymentFrequencyDTO)->from([
                    'payment_type_id' => $frequency->payment_type_id,
                    'company_id' => $frequency->company_id,
                    'frequency' => $frequency->frequency,
                    'should_pay' => $datum['should_pay'],
                    'deduction_account_id' => $frequency->deduction_account_id
                ]);

                (new UpdateCompanyPaymentFrequencyAction)->handle($frequency, $dto);
                $canUpdate = true;
            }

        }

        if ($updateStep && $canUpdate) {
            $companyId = $frequencies->first()->company_id;
            $company = Company::with('client')->find($companyId);
            $company->client->update(['onboarding_step' => OnboardingSteps::SETUP_APPROVALS->value]);
        }

        if($user) {
            (new AuditLogger($user->id, __('audits.payment_types_updated', ['user' => $user->last_name])))->logAsUpdated();
        }

        return true;
    }

    public function listStates(?string $q, ?int $country_id = null): array
    {
        return State::when($country_id, fn($query) => $query->where('country_id', $country_id))
                    ->when($q, fn($query) => $query->where('name', 'like', "%$q%"))
                    ->get()
                    ->toArray();
    }

    public function listCountries(?string $q): array
    {
        return Country::when($q, fn($query) => $query->where('name', 'like', "%$q%"))
                    ->get()
                    ->toArray();
    }
}
