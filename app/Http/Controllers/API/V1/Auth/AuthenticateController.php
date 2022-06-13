<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Enums\AppHeaders;
use App\Helpers\BreezeResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\GetClientIdRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function authenticate(LoginRequest $request): JsonResponse
    {
        extract($request->all());
        $data = $this->authService->login($email, $password, $request->header(AppHeaders::X_APP_ID->value));

        return (new BreezeResponse(
            data: $data,
            message: __('auth.login_success')
        ))->asSuccessful();
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout(Auth::user());
        return (new BreezeResponse(
            message: __('auth.logout_success')
        ))->asSuccessful();
    }

    public function forgot(ForgotPasswordRequest $request): JsonResponse
    {
        $status = $this->authService->forgotPassword($request->validated());

        if (!$status) {
            return (new BreezeResponse(
                message: __('passwords.throttled')
            ))->asBadRequest();
        }

        return (new BreezeResponse(
            message: __('passwords.sent')
        ))->asSuccessful();
    }

    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        $status = $this->authService->resetPassword($request->validated());

        if (!$status) {
            return (new BreezeResponse(
                message: __('passwords.token')
            ))->asBadRequest();
        }

        return (new BreezeResponse(
            message: __('passwords.reset')
        ))->asSuccessful();
    }

}
