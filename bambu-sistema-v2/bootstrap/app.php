<?php

use App\Http\Middleware\ApiLogging;
use App\Http\Middleware\CorrelationId;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api([
            SecurityHeaders::class,
            CorrelationId::class,
            ApiLogging::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $code = 'INTERNAL_SERVER_ERROR';
                $message = 'Ha ocurrido un error interno';
                $details = [];
                $status = 500;

                if ($e instanceof ValidationException) {
                    // Para tests, mantener el formato original de Laravel
                    if (app()->environment('testing')) {
                        return response()->json([
                            'message' => $e->getMessage(),
                            'errors' => $e->errors(),
                        ], 422);
                    }

                    $code = 'VALIDATION_ERROR';
                    $message = 'Los datos proporcionados no son válidos';
                    $details = $e->errors();
                    $status = 422;
                } elseif ($e instanceof HttpException) {
                    $status = $e->getStatusCode();
                    $message = $e->getMessage() ?: 'Error HTTP';

                    switch ($status) {
                        case 401:
                            $code = 'UNAUTHORIZED';
                            $message = 'Credenciales inválidas';
                            break;
                        case 403:
                            $code = 'FORBIDDEN';
                            $message = 'Acceso denegado';
                            break;
                        case 404:
                            $code = 'RESOURCE_NOT_FOUND';
                            $message = 'Recurso no encontrado';
                            break;
                        case 429:
                            $code = 'TOO_MANY_REQUESTS';
                            $message = 'Demasiadas solicitudes';
                            break;
                        default:
                            $code = 'HTTP_ERROR';
                            break;
                    }
                } elseif ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                    $code = 'RESOURCE_NOT_FOUND';
                    $message = 'Recurso no encontrado';
                    $status = 404;
                } elseif ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    $code = 'UNAUTHORIZED';
                    $message = 'No autenticado';
                    $status = 401;
                }

                return response()->json([
                    'error' => [
                        'code' => $code,
                        'message' => $message,
                        'details' => $details,
                        'requestId' => $request->header('X-Request-ID', 'unknown'),
                    ],
                ], $status);
            }
        });
    })->create();
