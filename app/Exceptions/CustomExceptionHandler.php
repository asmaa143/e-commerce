<?php

namespace App\Exceptions;
use App\Traits\Api\ApiResponseTrait;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;
class CustomExceptionHandler
{
    use ApiResponseTrait;
    /**
     * Register custom exception handling.
     *
     * @param Exceptions $exceptions
     * @return void
     */
    public function register(Exceptions $exceptions)
    {
        $exceptions->render(function (Exception $e, Request $request) {
            $isApiRequest = $request->is('api/*') || $request->wantsJson();
            if ($e instanceof ValidationException && $isApiRequest) {

                return $this->responseError($e->getMessage(),422,"Validation error");

            }
            // You can add other exception handling logic here if needed
        });
    }
}
