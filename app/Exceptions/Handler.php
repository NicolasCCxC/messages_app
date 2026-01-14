<?php

namespace App\Exceptions;

use App\Traits\ResponseApiTrait;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{

    use ResponseApiTrait;

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Throwable $exception
     * @return void
     *
     * @throws Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param Throwable $exception
     * @return JsonResponse
     *
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Default values to handler
        $message = env('APP_DEBUG') == true ? $exception->getMessage() : Response::$statusTexts[Response::HTTP_BAD_REQUEST];
        $errors = env('APP_DEBUG') == true ? $exception->getTrace() : [];

        $statusCode = Response::HTTP_BAD_REQUEST;

        switch (get_class($exception)) {
            case HttpException::class:
                $statusCode = $exception->getStatusCode();
                $message = Response::$statusTexts[$statusCode];
                break;
            case ModelNotFoundException::class:
                $model = strtolower(class_basename($exception->getModel()));
                $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;
                $message = "Does not exist any instance of {$model} with the given data";
                break;
            case ValidationException::class:
                $errors = $exception->validator->errors()->getMessages();
                $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;
                $message = $exception->getMessage();
                break;
            case PayErrorException::class:
                $errors = [];
        }

        return $this->errorResponse($statusCode, $message, $errors);
    }
}
