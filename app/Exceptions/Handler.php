<?php

namespace App\Exceptions;

use App\Models\Module;
use App\Traits\ResponseApiTrait;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ResponseApiTrait;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
        $this->renderable(function (Throwable $e) {
            return $this->handleException($e);
        });
    }

    public function handleException(Throwable $e): JsonResponse
    {
        $serviceName = Module::SECURITY;
        $message = env('APP_DEBUG') == true ? $e->getMessage() : Response::$statusTexts[Response::HTTP_BAD_REQUEST];
        $errors = env('APP_DEBUG') == true ? $e->getTrace() : [];
        $statusCode = Response::HTTP_BAD_REQUEST;
        switch (get_class($e)) {
            case UnauthorizedHttpException::class :
                $message = 'Unauthorized access';
                $statusCode = Response::HTTP_UNAUTHORIZED;
                break;
            case AuthorizationException::class :
                $message = $e->getMessage();
                $statusCode = $e->getCode();
                break;

            case ValidationException::class :
                $errors = $e->validator->errors()->getMessages();
                $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;
                $message = $e->getMessage();
                break;

            case ModelNotFoundException::class:
                $model = strtolower(class_basename($e->getModel()));
                $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;
                $message = "Does not exist any instance of ${model} with the given data";
                break;

            case BadRequestHttpException::class:
                $serviceName = Module::WEBSITE;
                $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;
                $message = $e->getMessage();
                break;

            case HttpException::class:
                $statusCode = $e->getStatusCode();
                $message = env('APP_DEBUG') == true ? $e->getMessage() : Response::$statusTexts[$statusCode];
                break;

            case GuzzleException::class :
                $statusCode = $e->getCode();
                $message = $e->getMessage();

        }

        return $this->errorResponse($serviceName, $statusCode, $message, $errors);
    }
}
