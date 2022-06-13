<?php
use \App\Models\State;
use \App\Models\Country;

uses(Tests\TestCase::class);
uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

$configService = new \App\Services\ConfigService;

test("User can list states successfully", function () use ($configService) {
    State::factory()->count(20)->create();

    $states = $configService->listStates(null);

    expect($states)->toBeArray();

    $this->assertNotEmpty($states);
});

test("User can filter state by country id successfully", function () use ($configService) {
    $country = Country::factory()->create();
    State::factory()->count(20)->create();
    State::factory()->create(['country_id' => $country->id]);

    $states = $configService->listStates(null, $country->id);

    expect($states)->toBeArray();

    $this->assertNotEmpty($states);
});

test("User can search state by name successfully", function () use ($configService) {
    $state = State::factory()->create();

    $states = $configService->listStates($state->name);

    expect($states)->toBeArray();

    $this->assertNotEmpty($states);

    expect($states[0]['name'])->toBe($state->name);
});


test("User can list countries successfully", function () use ($configService) {

    Country::factory()->count(20)->create();

    $countries = $configService->listCountries(null);

    expect($countries)->toBeArray();

    $this->assertNotEmpty($countries);
});

test("User can search countries by name successfully", function () use ($configService) {
    $country = Country::factory()->create();

    $data = $configService->listCountries($country->name);

    expect($data)->toBeArray();

    $this->assertNotEmpty($data);

    expect($data[0]['name'])->toBe($country->name);
});
