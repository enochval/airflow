<?php
use \App\Helpers\BreezeRequest;

uses(Tests\TestCase::class);
uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('Using Breeze get request calls url and return response', function () {
    $response = BreezeRequest::get(route('response.get'));

    expect($response)->toBeArray();


    $this->assertDatabaseHas('api_logs', [
        'url' => route('response.get'),
        'status' => "200"
    ]);
});


test('Using Breeze post request calls url and return response', function () {
    $response = BreezeRequest::post(url:route('response.post'), request:[]);

    expect($response)->toBeArray();


    $this->assertDatabaseHas('api_logs', [
        'url' => route('response.post'),
        'status' => "200"
    ]);
});
