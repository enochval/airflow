<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\BreezeResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCompanyPaymentTypeRequest;
use App\Services\ConfigService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SetupController extends Controller
{
    public function __construct(protected ConfigService $configService){}

    public function setupVirtualAccount(Request $request): JsonResponse
    {
        $data = $this->configService->setupVirtualAccount($request->company, $request->user);

        return (new BreezeResponse(
            data: $data, message: __('general.action_successful')
        ))->asSuccessful();
    }

    public function getPaymentTypes(Request $request) : JsonResponse
    {
        $data = $this->configService->listPaymentTypes($request->company);

        return (new BreezeResponse(
            data: $data, message: __('general.action_successful')
        ))->asSuccessful();
    }

    public function updatePaymentTypes(UpdateCompanyPaymentTypeRequest $request) : JsonResponse
    {
        $data = $this->configService->updatePaymentFrequencies($request->payment_types);

        if ($data) {
            return (new BreezeResponse(
                message: __('general.action_successful')
            ))->asSuccessful();
        } else {
            return (new BreezeResponse(
                message: __('general.action_failed')
            ))->asBadRequest();
        }
    }
}
