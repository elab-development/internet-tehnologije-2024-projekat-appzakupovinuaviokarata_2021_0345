<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

// JWT (tymon/jwt-auth)
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class Handler extends ExceptionHandler
{
    public function register(): void
    {
        //
    }

    public function render($request, Throwable $e)
    {
        if ($request->expectsJson() || $request->is('api/*')) {

            if ($e instanceof ValidationException) {
                return response()->json([
                    'message' => 'Validacija neuspešna.',
                    'errors'  => $e->errors(),
                ], 422);
            }

            if ($e instanceof AuthenticationException ||
                $e instanceof TokenInvalidException ||
                $e instanceof TokenExpiredException ||
                $e instanceof JWTException) {
                return response()->json([
                    'message' => $e instanceof TokenExpiredException ? 'JWT token je istekao.'
                              : ($e instanceof TokenInvalidException ? 'JWT token je nevažeći.' : 'Nije autentifikovan.'),
                ], 401);
            }

            if ($e instanceof AuthorizationException) {
                return response()->json(['message' => 'Zabranjen pristup.'], 403);
            }

            if ($e instanceof ModelNotFoundException) {
                $model = class_basename($e->getModel());
                return response()->json(['message' => "{$model} nije pronađen."], 404);
            }

            if ($e instanceof NotFoundHttpException) {
                return response()->json(['message' => 'Ruta nije pronađena.'], 404);
            }

            if ($e instanceof MethodNotAllowedHttpException) {
                return response()->json(['message' => 'HTTP metod nije dozvoljen.'], 405);
            }

            if ($e instanceof ThrottleRequestsException) {
                return response()->json(['message' => 'Previše zahteva. Pokušaj kasnije.'], 429);
            }

            if ($e instanceof HttpExceptionInterface) {
                return response()->json([
                    'message' => $e->getMessage() ?: 'Greška u zahtevu.',
                ], $e->getStatusCode());
            }

            return response()->json([
                'message' => config('app.debug') ? ($e->getMessage() ?: 'Interna greška servera.') : 'Interna greška servera.',
            ], 500);
        }

        return parent::render($request, $e);
    }
}
