<?php

namespace App\Exceptions;

use App\Helpers\BreezeResponse;
use Exception;
use Illuminate\Http\JsonResponse;

class BreezeNotFoundException extends Exception
{
    public function __construct(
        protected string $msg
    )
    {
        parent::__construct();
    }

    public function render(): JsonResponse
    {
        return (new BreezeResponse(
            message: $this->msg
        ))->asNotFound();
    }
}
