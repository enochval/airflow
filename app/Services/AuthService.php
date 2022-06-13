<?php

namespace App\Services;

use App\Exceptions\BreezeException;
use App\Exceptions\BreezeNotFoundException;
use App\Helpers\AuditLogger;
use App\Models\Client;
use App\Models\User;
use App\Traits\ThrottlesLoginAttempts;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthService
{
    use ThrottlesLoginAttempts;

    public function login(string $email, string $password, ?string $client = null): array
    {
        $throttleKey = Str::transliterate(Str::lower($email));

        if($this->hasTooManyLoginAttempts($throttleKey))
        {
            $this->sendLockoutResponse($throttleKey, 'email');
        }

        $user = $this->findUserByEmail($email);

        if (!$user) {
            throw new BreezeNotFoundException(__('general.not_found', ['model' => 'User']));
        }

        $client = Client::with('companies')->find(base64_decode($client));

        if (!$client) {
            throw new BreezeNotFoundException(__('general.not_found', ['model' => 'Client']));
        }

        $companies = $client->companyIds();

        if (!in_array($user->company->id, $companies)) {
            throw new BreezeNotFoundException(__('general.not_allowed'));
        }

        if (!$user->is_active) {
            throw new BreezeException(__('general.try_again', ['extra' => 'User is in-active, please Activate']));
        }

        if (!$attempt = Auth::attempt(['email' => $email, 'password' => $password])) {
            $this->incrementLoginAttempts($email);
            throw new BreezeException(__('auth.failed'));
        }

        $user = Auth::user();

        $this->updateLastLogin($user);

        $user->access_token = $this->generateToken($user);

        (new AuditLogger($user->id, __('auth.login_success')))->logAsUpdated();

        $this->clearLoginAttempts($throttleKey);

        return $user->toArray();
    }

    public function forgotPassword(array $credentials): bool
    {
        ['email' => $email] = $credentials;

        $user = $this->findUserByEmail($email);

        if (!$user) {
            throw new BreezeNotFoundException(__('passwords.user'));
        }

        $client = $user->load('company.client')->company->client;

        ResetPassword::createUrlUsing(function ($user, string $token) use ($client) {
            return $client->url . config('app.frontend_reset_password_url').'?token='.$token;
        });

        $status = Password::sendResetLink($credentials);

        if ($check = $status === Password::RESET_LINK_SENT) {
            (new AuditLogger($user->id, __('audits.forgot_password')))->logAsUpdated();
        }

        return $check;
    }

    public function resetPassword(array $credentials): bool
    {
        ['email' => $email] = $credentials;

        $user = $this->findUserByEmail($email);

        if (!$user) {
            throw new BreezeNotFoundException(__('passwords.user'));
        }

        $status = Password::reset($credentials,
            function ($user, $password): void {
                $user->forceFill([
                    'password' => Hash::make($password)
                ]);

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($check = $status === Password::PASSWORD_RESET) {
            (new AuditLogger($user->id, __('audits.reset_password')))->logAsUpdated();
        }

        return $check;
    }

    public function loginWrong(string $username, string $password): bool
    {
        return false;
    }

    public function logout(Authenticatable|User $user): bool
    {
        $logout = $user->tokens()->delete();

        if ($logout) {
            (new AuditLogger($user->id, __('auth.logout_success')))->logAsUpdated();
        }

        return $logout;
    }

    private function findUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    private function generateToken(Authenticatable|User $user): string
    {
        return $user->createToken($user->email)->plainTextToken;
    }

    private function updateLastLogin(Authenticatable|User $user): bool
    {
        return $user->update([
            'last_login' => Carbon::now()->toDateTimeString()
        ]);
    }
}
