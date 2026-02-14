<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    use ApiResponse;

    private function getSpecificController(string $module): ?string
    {
        $map = [
            'people' => \App\Http\Controllers\Api\V1\Modules\PeopleController::class,
        ];

        return $map[$module] ?? null;
    }

    private function delegate(string $method, string $module, ...$params): JsonResponse
    {
        $controllerClass = $this->getSpecificController($module);

        if (!$controllerClass) {
            return $this->notFound("Módulo '{$module}' não encontrado");
        }

        $controller = app()->make($controllerClass);

        if (!method_exists($controller, $method)) {
            return $this->notFound("Ação '{$method}' não disponível para o módulo '{$module}'");
        }

        return $controller->$method(request(), ...$params);
    }

    public function index(Request $request, string $module): JsonResponse
    {
        return $this->delegate('index', $module);
    }

    public function store(Request $request, string $module): JsonResponse
    {
        return $this->delegate('store', $module);
    }

    public function show(Request $request, string $module, string $code): JsonResponse
    {
        return $this->delegate('show', $module, $code);
    }

    public function update(Request $request, string $module, string $code): JsonResponse
    {
        return $this->delegate('update', $module, $code);
    }

    public function destroy(Request $request, string $module, string $code): JsonResponse
    {
        return $this->delegate('destroy', $module, $code);
    }

    public function restore(Request $request, string $module, string $code): JsonResponse
    {
        return $this->delegate('restore', $module, $code);
    }

    public function reorder(Request $request, string $module): JsonResponse
    {
        return $this->delegate('reorder', $module);
    }
}
