<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return JsonResponse
     */
    public function render($request, Throwable $e): JsonResponse
    {
        return match (get_class($e)) {
            ValidationException::class => $this->errorResponse(Response::HTTP_BAD_REQUEST, $e->validator->errors()->first(), $e->validator->errors()),
            NotFoundResourceException::class => $this->errorResponse(Response::HTTP_NOT_FOUND, "요청하신 리소스가 존재하지 않습니다."),
            \Illuminate\Validation\ValidationException::class => $this->errorResponse(Response::HTTP_BAD_REQUEST, $e->validator->errors()->first(), $e->validator->errors()),
            default => $this->errorResponse(Response::HTTP_BAD_REQUEST, "오류가 발생했습니다.", [
                "exception" => get_class($e),
                "code" => $e->getCode(),
                "message" => $e->getMessage(),
                "file" => $e->getFile(),
                "line" => $e->getLine(),
                "trace" => $e->getTrace(),
            ])
        };
    }

    private function errorResponse(
        int   $httpCode,
        mixed $message,
        mixed $data = null,
        int   $errorCode = null
    ): JsonResponse
    {
        $response = [
            'code' => $errorCode ?: $httpCode,
            'data' => $data,
            'message' => $message,
        ];

        return response()->json($response, $httpCode);
    }
}
