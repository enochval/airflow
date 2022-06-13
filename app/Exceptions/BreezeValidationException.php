<?php

namespace App\Exceptions;

use App\Helpers\BreezeResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Throwable;

class BreezeValidationException extends Exception
{
    public function __construct(
        protected array $errors,
        protected string $msg = "A validation error has occurred."
    )
    {
        parent::__construct();
    }

    public function render(): JsonResponse
    {
        return (new BreezeResponse(
            data: [],
            message: $this->msg,
            error: $this->errors
        ))->asValidationError();
    }
}
