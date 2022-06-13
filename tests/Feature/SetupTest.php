<?php

use App\Enums\OnboardingSteps;
use \App\Enums\PaymentFrequencyEnum;
use \App\Actions\CreateCompanyAction;
use App\Models\Bank;
use App\Models\PaymentAccount;
use \App\Models\PaymentType;
use \App\DTOs\PaymentFrequencyDTO;
use \App\Actions\CreateCompanyPaymentFrequencyAction;
use \App\Models\Company;

uses(Tests\TestCase::class);
uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

$configService = new \App\Services\ConfigService();

test('Create company payment types frequency action creates company related frequencies', function ($company) {

    $paymentType = PaymentType::factory()->create();
    $dto = (new PaymentFrequencyDTO())->from([
        'payment_type_id' => $paymentType->id,
        'company_id' => $company->id,
        'frequency' => PaymentFrequencyEnum::MONTHLY,
        'should_pay' => in_array($paymentType, (new CreateCompanyAction)->defaultShouldPays()),
        'deduction_account_id' => null
    ]);

    (new CreateCompanyPaymentFrequencyAction())->handle($dto);

    $this->assertDatabaseHas('payment_frequencies', [
        'payment_type_id' => $paymentType->id,
        'company_id' => $company->id,
        'frequency' => PaymentFrequencyEnum::MONTHLY,
    ]);
})->with([
    fn() => Company::factory()->create(),
]);

test('User can update company payment frequencies', function ($company) use ($configService) {

    $paymentType = PaymentType::factory()->create();

    $frequency = \App\Models\PaymentFrequency::factory()->create([
       'payment_type_id' => $paymentType->id,
       'company_id' => $company->id,
       'should_pay' => false,
    ]);

    $requestData = [$configService->paymentFrequenciesSchema($frequency)];

    $updated = $configService->updatePaymentFrequencies($requestData);

    expect($updated)->toBe(true);

    $this->assertDatabaseHas('payment_frequencies', [
        'payment_type_id' => $paymentType->id,
        'company_id' => $company->id,
        'frequency' => PaymentFrequencyEnum::MONTHLY,
        'should_pay' => false,
    ]);
})->with([
    fn() => Company::factory()->create(),
]);

test('User action is logged when they update company payment frequencies', function ($company) use ($configService) {

    $paymentType = PaymentType::factory()->create();

    $frequency = \App\Models\PaymentFrequency::factory()->create([
        'payment_type_id' => $paymentType->id,
        'company_id' => $company->id,
        'should_pay' => false,
    ]);

    $requestData = [$configService->paymentFrequenciesSchema($frequency)];
    $user = \App\Models\User::factory()->create();

    $updated = $configService->updatePaymentFrequencies($requestData, true, $user);

    expect($updated)->toBe(true);

    $this->assertDatabaseHas('audit_logs', [
        'user_id' => $user->id,
        'action' => \App\Helpers\AuditLogger::UPDATED,
    ]);

})->with([
    fn() => Company::factory()->create(),
]);

test("Client onboarding step can be updated after updating payment frequencies", function ($company) use ($configService) {
    $paymentType = PaymentType::factory()->create();

    $frequency = \App\Models\PaymentFrequency::factory()->create([
        'payment_type_id' => $paymentType->id,
        'company_id' => $company->id,
        'should_pay' => false,
    ]);

    $requestData = [$configService->paymentFrequenciesSchema($frequency)];

    $updated = $configService->updatePaymentFrequencies($requestData, true);

    expect($updated)->toBe(true);
    $client = \App\Models\Client::find($company->client_id);
    $this->assertDatabaseHas('clients', [
        'id' => $client->id,
        'onboarding_step' => OnboardingSteps::SETUP_APPROVALS->value,
    ]);
})->with([
    fn() => Company::factory()->create(),
]);

test('Company with a virtual account cannot create another', function ($company) use ($configService) {

    PaymentAccount::factory()->create([
        'company_id' => $company->id
    ]);

    $configService->setupVirtualAccount($company);

})->with([
    fn() => Company::factory()->create(),
])->throws(\App\Exceptions\BreezeException::class);

