<?php

namespace App\Http\Controllers;

use App\Helpers\BreezeResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestResponseController extends Controller
{
    public function getResponse() : JsonResponse
    {
        return (new BreezeResponse(message: "I am successful"))
                    ->asSuccessful();
    }

    public function postResponse() : JsonResponse
    {
        return (new BreezeResponse(data: ["hello" => 'Hi'], message: "I am successful"))
            ->asSuccessful();
    }
}
