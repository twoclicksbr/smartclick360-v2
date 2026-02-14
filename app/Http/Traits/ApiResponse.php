<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success($data = null, string $message = null, int $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
        ];

        if ($message) {
            $response['message'] = $message;
        }

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    protected function error(string $message = 'Erro interno', int $code = 500, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    protected function created($data = null, string $message = 'Registro criado com sucesso'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    protected function deleted(string $message = 'Registro removido com sucesso'): JsonResponse
    {
        return $this->success(null, $message);
    }

    protected function restored(string $message = 'Registro restaurado com sucesso'): JsonResponse
    {
        return $this->success(null, $message);
    }

    protected function notFound(string $message = 'Registro não encontrado'): JsonResponse
    {
        return $this->error($message, 404);
    }

    protected function unauthorized(string $message = 'Não autorizado'): JsonResponse
    {
        return $this->error($message, 401);
    }

    protected function forbidden(string $message = 'Acesso negado'): JsonResponse
    {
        return $this->error($message, 403);
    }

    protected function validationError($errors, string $message = 'Dados inválidos'): JsonResponse
    {
        return $this->error($message, 422, $errors);
    }
}
