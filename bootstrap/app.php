<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (ValidationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }
        });
        $exceptions->renderable(function (Throwable $e, Request $request) {
            if ($request->expectsJson()) {
                Log::error('API Error: '.$e->getMessage(), [
                    'url' => $request->fullUrl(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка сервера. Пожалуйста, попробуйте позже.',
                ], 500);
            }
        });
    })->create();
