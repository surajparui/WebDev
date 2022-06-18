<?php

namespace App\Exceptions;

use App\Traits\ApiResponser;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponser;

    /**
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * @return void
     */
    public function register()
    {
        $this->renderable(function (Throwable $exception, $request) {

            if ($exception instanceof HttpException) {
                $code    = $exception->getStatusCode();
                $message = Response::$statusTexts[$code];

                return $this->errorResponse($message, $code);
            }

            if ($exception instanceof ModelNotFoundException) {
                $model = strtolower(class_basename($exception->getModel()));

                return $this->errorResponse("Does not exist any instance of {$model} with the given id", Response::HTTP_NOT_FOUND);
            }

            if ($exception instanceof AuthorizationException) {
                return $this->errorResponse($exception->getMessage(), Response::HTTP_FORBIDDEN);
            }

            if ($exception instanceof AuthenticationException) {
                return $this->errorResponse($exception->getMessage(), Response::HTTP_UNAUTHORIZED);
            }

            if ($exception instanceof ValidationException) {
                $errors = $exception->validator->errors()->getMessages();

                return $this->errorResponse($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if ($exception instanceof \Exception) {
                return $this->errorResponse($exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if (!config('app.debug')) {
                return $this->errorResponse('Unexpected error. Try later', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        });
    }
}
