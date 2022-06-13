<?php

namespace App\Http\Controllers\API\V1;

use App\Helpers\BreezeResponse;
use App\Http\Controllers\Controller;
use App\Services\ConfigService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConfigurationController extends Controller
{
    public function __construct(protected ConfigService $configService){}

    public function getStates(Request $request) : JsonResponse
    {
        $data = $this->configService->listStates($request->q, $request->country_id);

        return (new BreezeResponse(
            data: $data, message: __('general.action_successful')
        ))->asSuccessful();
    }

    public function getCountries(Request $request) : JsonResponse
    {
        $data = $this->configService->listCountries($request->q);

        return (new BreezeResponse(
            data: $data, message: __('general.action_successful')
        ))->asSuccessful();
    }
}
