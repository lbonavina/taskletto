<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->renderable(function (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Recurso não encontrado.',
                'error'   => 'not_found',
            ], 404);
        });

        $this->renderable(function (NotFoundHttpException $e) {
            return response()->json([
                'message' => 'Rota não encontrada.',
                'error'   => 'route_not_found',
            ], 404);
        });

        $this->renderable(function (MethodNotAllowedHttpException $e) {
            return response()->json([
                'message' => 'Método HTTP não permitido para esta rota.',
                'error'   => 'method_not_allowed',
            ], 405);
        });

        $this->renderable(function (ValidationException $e) {
            return response()->json([
                'message' => 'Os dados fornecidos são inválidos.',
                'errors'  => $e->errors(),
            ], 422);
        });

        $this->renderable(function (Throwable $e) {
            if (! app()->environment('production')) {
                return null; // Usa o handler padrão em dev
            }

            return response()->json([
                'message' => 'Ocorreu um erro interno no servidor.',
                'error'   => 'internal_server_error',
            ], 500);
        });
    }
}
