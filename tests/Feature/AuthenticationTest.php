<?php

use App\Exceptions\BreezeException;
use App\Exceptions\BreezeNotFoundException;
use \App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use \Illuminate\Support\Collection;
use \Illuminate\Support\Facades\Hash;
use \Illuminate\Support\Facades\Event;
use \Illuminate\Auth\Events\Lockout;
use Illuminate\Validation\ValidationException;

uses(Tests\TestCase::class);
uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(fn() => User::factory()->count(3)->make());

$authService = new \App\Services\AuthService();

function getHashedClientFromUser(User $user):string {
    $client = $user->load('company.client')->company->client;
    return base64_encode($client->id);
}

test('User can login to the system with correct credentials', function (User $user) use ($authService) {
    $client = getHashedClientFromUser($user);

    $attempt = $authService->login($user->email, 'password', $client);

    expect($attempt)->toBeArray();
    $this->assertDatabaseHas('audit_logs', [
        'user_id' => $user->id,
        'action' => \App\Helpers\AuditLogger::UPDATED,
    ]);
})->with([
    fn() => User::factory()->create(['email' => 'test@email.com'])
]);

test('User cannot login to the system with wrong client', function (User $user) use ($authService) {
    $client = "wrong client";

    $attempt = $authService->login($user->email, 'password', $client);

    $this->assertDatabaseMissing('audit_logs', [
        'user_id' => $user->id,
        'action' => \App\Helpers\AuditLogger::UPDATED,
    ]);
})->with([
    fn() => User::factory()->create(['email' => 'test@email.com'])
])->throws(BreezeNotFoundException::class);

test('User cannot login to the system with wrong credentials', function (User $user) use ($authService) {
    $client = getHashedClientFromUser($user);

    $authService->login($user->email, 'wrong password', $client);
    $this->assertDatabaseMissing('audit_logs', [
        'user_id' => $user->id,
        'action' => \App\Helpers\AuditLogger::UPDATED,
    ]);

})->with([
    fn() => User::factory()->create(['email' => 'test@email.com'])
])->throws(BreezeException::class);


test('User logged in to the system is active', function (User $user) use ($authService) {
    $client = getHashedClientFromUser($user);
    $attempt = $authService->login($user->email, 'password', $client);

    expect($attempt)->toBeArray();

    if ($attempt) {
        expect($user->is_active)->toBe(true);
    }

})->with([
    fn() => User::factory()->create(['email' => 'test@email.com'])
]);

test('Logged in User can log out of the system', function (User $user) use ($authService) {
    $client = getHashedClientFromUser($user);

    //logout un authenticated user
    $authService->login($user->email, 'password', $client);
    $attempt = $authService->logout($user);

    expect($attempt)->toBe(true);

    $this->assertDatabaseHas('audit_logs', [
        'user_id' => $user->id,
        'action' => \App\Helpers\AuditLogger::UPDATED,
    ]);
})->with([
    fn() => User::factory()->create(['email' => 'test@email.com', 'password' => Hash::make('password')])
]);


test('Not logged in User cannot log out of the system', function (User $user) use ($authService) {
    //logout un authenticated user
    $attempt = $authService->logout($user);
    expect($attempt)->toBe(false);


    $this->assertDatabaseMissing('audit_logs', ['user_id' => $user->id,
        'action' => \App\Helpers\AuditLogger::UPDATED,
    ]);
})->with([
    fn() => User::factory()->create(['email' => 'test@email.com', 'password' => Hash::make('password')])
]);

test('Forgot password with invalid email throws not found exception', function () use ($authService) {
    $authService->forgotPassword(['email' => 'invalid@email.com']);
})->throws(BreezeNotFoundException::class);


test('Valid user can forgot password', function (User $user) use ($authService) {
    $attempt = $authService->forgotPassword(\Illuminate\Support\Arr::only($user->toArray(), 'email'));

    expect($attempt)->toBe(true);

    $this->assertDatabaseHas('audit_logs', [
        'user_id' => $user->id,
        'action' => \App\Helpers\AuditLogger::UPDATED,
    ]);
})->with([
    fn() => User::factory()->create(['email' => 'test@email.com', 'password' => Hash::make('password')])
]);

test('Valid user forgot password sends reset link in their email', function (User $user) use ($authService) {
    \Illuminate\Support\Facades\Notification::fake();

    $authService->forgotPassword(\Illuminate\Support\Arr::only($user->toArray(), 'email'));

    \Illuminate\Support\Facades\Notification::assertSentTo([$user], ResetPasswordNotification::class);
})->with([
    fn() => User::factory()->create(['email' => 'test@email.com', 'password' => Hash::make('password')])
]);

test('Forgot password throttled for multiple request from same user', function (User $user) use ($authService) {
    $times = 0;
    $attempts = null;

    while ($times < 2) {
        $attempts = $authService->forgotPassword(\Illuminate\Support\Arr::only($user->toArray(), 'email'));
        $times++;
    }

    expect($attempts)->toBe(false);
})->with([
    fn() => User::factory()->create(['email' => 'test@email.com', 'password' => Hash::make('password')])
]);

test('Reset password with invalid email throws not found exception', function () use ($authService) {
    $authService->resetPassword(['email' => 'invalid@email.com']);
})->throws(BreezeNotFoundException::class);

test('reset password updates user password with new password', function (User $user) use ($authService) {
    $token = \Illuminate\Support\Facades\Password::broker()->createToken($user);
    $new_password = 'new password';

    $credentials = [
        'email' => $user->email,
        'token' => $token,
        'password' => $new_password
    ];

    $authService->resetPassword($credentials);

    $user->refresh();

    $check = Hash::check($new_password, $user->password);

    expect($check)->toBe(true);
})->with([
    fn() => User::factory()->create(['email' => 'test@email.com', 'password' => Hash::make('password')])
]);

test('User can reset password', function (User $user) use ($authService) {
    $token = \Illuminate\Support\Facades\Password::broker()->createToken($user);

    $credentials = [
        'email' => $user->email,
        'token' => $token,
        'password' => 'new password'
    ];

    $attempt = $authService->resetPassword($credentials);

    expect($attempt)->toBe(true);

    $this->assertDatabaseHas('audit_logs', [
        'user_id' => $user->id,
        'action' => \App\Helpers\AuditLogger::UPDATED,
    ]);

})->with([
    fn() => User::factory()->create(['email' => 'test@email.com', 'password' => Hash::make('password')])
]);

test('reset password returns false with invalid or expired token', function (User $user) use ($authService) {

    $credentials = [
        'email' => $user->email,
        'token' => \Illuminate\Support\Str::random(),
        'password' => 'new password'
    ];

    $attempt = $authService->resetPassword($credentials);

    expect($attempt)->toBe(false);

})->with([
    fn() => User::factory()->create(['email' => 'test@email.com', 'password' => Hash::make('password')])
]);

