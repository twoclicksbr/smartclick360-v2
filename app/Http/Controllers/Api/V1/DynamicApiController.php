<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\DynamicRequest;
use App\Http\Traits\ApiResponse;
use App\Models\DynamicModel;
use App\Services\DynamicService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DynamicApiController extends Controller
{
    use ApiResponse;

    /**
     * Resolve o model: específico ou DynamicModel.
     */
    protected function resolveModel(string $slug, string $connection = 'tenant'): mixed
    {
        $module = \Illuminate\Support\Facades\DB::connection($connection)
            ->table('modules')
            ->where('slug', $slug)
            ->whereNull('deleted_at')
            ->first();

        if (!$module) {
            abort(404, "Módulo '{$slug}' não encontrado.");
        }

        // Se model específico, instancia ele
        if ($module->model && $module->model !== 'Genérica') {
            $modelClass = $module->model;
            if (class_exists($modelClass)) {
                return new $modelClass();
            }
        }

        // Senão, usa DynamicModel
        return DynamicModel::forModule($slug, $connection);
    }

    /**
     * Resolve o service: específico ou DynamicService.
     */
    protected function resolveService(string $slug, string $connection = 'tenant'): mixed
    {
        $module = \Illuminate\Support\Facades\DB::connection($connection)
            ->table('modules')
            ->where('slug', $slug)
            ->whereNull('deleted_at')
            ->first();

        // Se service específico, instancia ele
        if ($module && $module->service && class_exists($module->service)) {
            return new ($module->service)();
        }

        // Senão, usa DynamicService com DynamicModel
        $model = $this->resolveModel($slug, $connection);

        if ($model instanceof DynamicModel) {
            return new DynamicService($model);
        }

        // Model específico sem service específico: usa DynamicService com DynamicModel
        $dynamicModel = DynamicModel::forModule($slug, $connection);
        return new DynamicService($dynamicModel);
    }

    /**
     * Valida a request: específico ou DynamicRequest.
     */
    protected function validateRequest(Request $request, string $slug, string $connection = 'tenant'): array
    {
        $module = \Illuminate\Support\Facades\DB::connection($connection)
            ->table('modules')
            ->where('slug', $slug)
            ->whereNull('deleted_at')
            ->first();

        // Se request específico, usa ele
        if ($module && $module->request && class_exists($module->request)) {
            $formRequest = app($module->request);
            return $formRequest->validate($formRequest->rules());
        }

        // Senão, usa DynamicRequest
        $dynamicRequest = new DynamicRequest();
        $dynamicRequest->setModuleSlug($slug)->setConnectionName($connection);
        $rules = $dynamicRequest->rules();
        $attributes = $dynamicRequest->attributes();

        return $request->validate($rules, [], $attributes);
    }

    /**
     * GET /api/v1/{module}
     */
    public function index(Request $request, string $module): JsonResponse
    {
        $connection = $this->getConnection();
        $service = $this->resolveService($module, $connection);

        $params = [
            'sort_by'        => $request->get('sort_by'),
            'sort_direction'  => $request->get('sort_direction'),
            'per_page'       => $request->get('per_page'),
            'quick_search'   => $request->get('quick_search'),
            'with_trashed'   => $request->get('with_trashed'),
        ];

        $records = $service->index($params);

        return $this->success($records, 'Registros listados com sucesso.');
    }

    /**
     * POST /api/v1/{module}
     */
    public function store(Request $request, string $module): JsonResponse
    {
        $connection = $this->getConnection();
        $validated = $this->validateRequest($request, $module, $connection);
        $service = $this->resolveService($module, $connection);

        $record = $service->create($validated);

        return $this->created($record, 'Registro criado com sucesso.');
    }

    /**
     * GET /api/v1/{module}/{code}
     */
    public function show(string $module, string $code): JsonResponse
    {
        $connection = $this->getConnection();
        $service = $this->resolveService($module, $connection);

        $id = decodeId($code);
        $record = $service->find($id);

        return $this->success($record, 'Registro encontrado.');
    }

    /**
     * PUT /api/v1/{module}/{code}
     */
    public function update(Request $request, string $module, string $code): JsonResponse
    {
        $connection = $this->getConnection();
        $validated = $this->validateRequest($request, $module, $connection);
        $service = $this->resolveService($module, $connection);

        $id = decodeId($code);
        $record = $service->update($id, $validated);

        return $this->success($record, 'Registro atualizado com sucesso.');
    }

    /**
     * DELETE /api/v1/{module}/{code}
     */
    public function destroy(string $module, string $code): JsonResponse
    {
        $connection = $this->getConnection();
        $service = $this->resolveService($module, $connection);

        $id = decodeId($code);
        $service->destroy($id);

        return $this->deleted('Registro removido com sucesso.');
    }

    /**
     * PATCH /api/v1/{module}/{code}/restore
     */
    public function restore(string $module, string $code): JsonResponse
    {
        $connection = $this->getConnection();
        $service = $this->resolveService($module, $connection);

        $id = decodeId($code);
        $record = $service->restore($id);

        return $this->restored('Registro restaurado com sucesso.');
    }

    /**
     * POST /api/v1/{module}/reorder
     */
    public function reorder(Request $request, string $module): JsonResponse
    {
        $connection = $this->getConnection();
        $service = $this->resolveService($module, $connection);

        $service->reorder($request->get('items', []));

        return $this->success(null, 'Registros reordenados com sucesso.');
    }

    /**
     * Determina a conexão com base no contexto (tenant ou landlord).
     */
    protected function getConnection(): string
    {
        // Se está em subdomínio de tenant, usa conexão tenant
        if (config('database.connections.tenant.database')) {
            return 'tenant';
        }

        return 'landlord';
    }
}
