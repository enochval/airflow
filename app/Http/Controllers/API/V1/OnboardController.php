<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\BreezeResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\OnboardRequest;
use App\Http\Requests\GetClientIdRequest;
use App\Services\ConfigService;
use Illuminate\Http\JsonResponse;

class OnboardController extends Controller
{
    public function __construct(protected ConfigService $configService){}

    public function onboardNewCompany(OnboardRequest $request): JsonResponse
    {
        $this->configService->onboardCompany($request->validated());

        return (new BreezeResponse(
            message: __('config.onboard.success')
        ))->asSuccessful();
    }

    public function getClientId(GetClientIdRequest $request)
    {
        $clientId = $this->configService->getClientId($request->url);

        $data = [
            'app_id' => $clientId
        ];

        if ($clientId) {
            return (new BreezeResponse(
                data: $data, message: __('auth.client_found')
            ))->asSuccessful();
        } else {
            return (new BreezeResponse(
                message: __('auth.client_not_found')
            ))->asBadRequest();
        }

    }

}
