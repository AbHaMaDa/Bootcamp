<?php

use Illuminate\Foundation\Application;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        $exceptions->renderable(function (AuthenticationException $e, $request) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        });

        $exceptions->renderable(function (ModelNotFoundException $e, $request) {
            return response()->json(['message' => 'Not found'], 404);
        });

        $exceptions->renderable(function (NotFoundHttpException $e, $request) {
            return response()->json(['message' => 'Not found'], 404);
        });

        $exceptions->renderable(function (ValidationException $e, $request) {
            $errors = $e->errors();

            $formattedErrors = [];
        
            foreach ($errors as $field => $message) {
                $formattedErrors[$field] = $message[0];
            }
            return response()->json(['errors' => $formattedErrors], 422);
        });

        $exceptions->renderable(function (\Exception $e, $request) {
            return response()->json(['message' => 'Internal server error'], 500);
        });
        
    })->create();
