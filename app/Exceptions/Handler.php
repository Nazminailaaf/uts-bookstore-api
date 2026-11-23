<?php

namespace App\Exceptions;

use Throwable;
use App\Helpers\XmlHelper;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    public function render($request, Throwable $exception)
    {
        if (env('APP_DEBUG')) {
            return parent::render($request, $exception);
        }

        $status  = Response::HTTP_INTERNAL_SERVER_ERROR;
        $message = 'HTTP_INTERNAL_SERVER_ERROR';

        if ($exception instanceof MethodNotAllowedHttpException) {
            $status  = Response::HTTP_METHOD_NOT_ALLOWED;
            $message = 'HTTP_METHOD_NOT_ALLOWED';
        } elseif ($exception instanceof NotFoundHttpException
            || $exception instanceof ModelNotFoundException) {
            $status  = Response::HTTP_NOT_FOUND;
            $message = 'HTTP_NOT_FOUND';
        } elseif ($exception instanceof AuthorizationException) {
            $status  = Response::HTTP_FORBIDDEN;
            $message = 'HTTP_FORBIDDEN';
        } elseif ($exception instanceof ValidationException) {
            $status  = Response::HTTP_BAD_REQUEST;
            $message = 'HTTP_BAD_REQUEST';
        }

        // error pun dikembalikan dalam format XML
        return XmlHelper::response(null, $status, $message);
    }
}
