<?php

use App\Actions\CreateCompanyPaymentFrequencyAction;
use App\DTOs\PaymentFrequencyDTO;
use App\Enums\PaymentFrequencyEnum;
use App\Models\Client;
use App\Models\Company;
use App\Models\User;
use App\Models\State;
use App\Models\Country;

uses(Tests\TestCase::class);
uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

$configService = new \App\Services\ConfigService();

test('Country not found throws a not found exception', function () use ($configService) {

    $payload = [
        "company" => [
            "country" => "invalid"
        ]
    ];

    $configService->onboardCompany($payload);

})->throws(\App\Exceptions\BreezeNotFoundException::class);

test('State not found throws a not found exception', function () use ($configService) {
    $country = Country::factory()->create();

    $payload = [
        "company" => [
            "country" => $country->name,
            "state" => "invalid",
        ]
    ];

    $configService->onboardCompany($payload);
})->throws(\App\Exceptions\BreezeNotFoundException::class);

test('Client details is created and stored in the database', function () use ($configService) {

    (new \App\Actions\CreateClientAction())->handle("SHR", "https://breeze.seamlesshr.com");

    $this->assertDatabaseHas('clients', [
        'name' => "SHR",
        'url' => "https://breeze.seamlesshr.com",
    ]);
});

test('Company details is created and attached to the client', function (\App\Models\Client $client) use ($configService) {
    $state = State::factory()->create();
    $state->load('country');

    $payload = [
        "client_id" => $client->id,
        "name" => "SHR",
        "url" => "https://breeze.seamlesshr.com",
        "logo" => "logo.png",
        "no_of_employees" => 50,
        "email" => "shr@seamlesshr.com",
        "phone_no" => "0902345678",
        "country" => $state->country->name,
        "state" => $state->name,
        "street" => "8 metabox",
        "city" => "ogba",
        "postal_code" => "100782",
        "tax_id" => "123456"
    ];

    (new \App\Actions\CreateCompanyAction())->handle($payload);

    $this->assertDatabaseHas('companies', [
        'name' => "SHR",
        'url' => "https://breeze.seamlesshr.com",
        'email' => "shr@seamlesshr.com",
        "phone_no" => "0902345678",
    ]);

    $this->assertInstanceOf(\App\Models\Company::class, $client->companies->first());

})->with([
    fn() => \App\Models\Client::factory()->create(['name' => 'SHR', 'url' => '"https://breeze.seamlesshr.com"'])
]);

test('User details is created and attached to the company', function () use ($configService) {
    $client = Client::factory()->create();

    Company::factory()->create([
        'client_id' => $client->id
    ]);

    $payload = [
        "first_name" => "Joseph",
        "last_name" => "Lana",
        "email" => "joseph@seamlesshr.com",
        "phone_no" => "09012345678",
        "password" => bcrypt("password"),
        "company_id" => $client->companies->first()->id
    ];

    (new \App\Actions\CreateUserAction())->handle($payload);

    $this->assertDatabaseHas('users', [
        'first_name' => "Joseph",
        "last_name" => "Lana",
        "email" => "joseph@seamlesshr.com"
    ]);

    $this->assertInstanceOf(User::class, $client->companies->first()->users->first());
});


