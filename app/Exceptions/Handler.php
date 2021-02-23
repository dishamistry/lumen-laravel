<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Handler extends ExceptionHandler
{
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
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof UnauthorizedHttpException) {
            // detect previous instance
            if ($exception->getPrevious() instanceof TokenExpiredException) {
                return response()->json([
                    'status' => $exception->getStatusCode(),
                    'message' => 'token_expired',
                ], $exception->getStatusCode());
            } else if ($exception->getPrevious() instanceof TokenInvalidException) {
                return response()->json([
                    'status' => $exception->getStatusCode(),
                    'message' => 'token_invalid',
                ], $exception->getStatusCode());
            } else if ($exception->getPrevious() instanceof TokenBlacklistedException) {
                return response()->json([
                    'status' => $exception->getStatusCode(),
                    'message' => 'token_blacklisted',
                ], $exception->getStatusCode());
            } else {
                return response()->json([
                    'status' => $exception->getStatusCode(),
                    'message' => 'token_error',
                ], $exception->getStatusCode());
            }
        }

        switch (true) {
            case $exception instanceof ModelNotFoundException:
                return response()->json([
                    'status' => 404,
                    'message' => 'Record not found',
                ], 404);
                break;
            case $exception instanceof TokenInvalidException:
                return response()->json([
                    'status' => 401,
                    'message' => 'Not authorized',
                ], 401);
                break;
            case $exception instanceof JWTException:
                return response()->json([
                    'status' => 401,
                    'message' => 'Not authorized',
                ], 401);
                break;
            case $exception instanceof NotFoundHttpException:
                return response()->json([
                    'status' => 404,
                    'message' => 'Not Found Exception',
                ], 404);
                break;
            case $exception instanceof ApiConnection:
                return response()->json([
                    'status' => 404,
                    'message' => 'Not Connection API stripe',
                ], 404);
                break;
        }

        return parent::render($request, $exception);
    }
}
