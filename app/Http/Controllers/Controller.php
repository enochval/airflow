<?php

namespace App\Http\Controllers;

use App\Helpers\BreezeResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function testResponse() : JsonResponse
    {
        //just an example of how it would be used
        return (new BreezeResponse(data: [
            "hello" => 'hi'
        ], message: "This is the message"))->asSuccessful();
    }
}
