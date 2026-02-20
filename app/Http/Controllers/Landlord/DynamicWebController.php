<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Http\Requests\DynamicRequest;
use App\Models\DynamicModel;
use App\Services\DynamicService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DynamicWebController extends Controller
{
    /**
     * Resolve a configuração completa do módulo.
     */
    protected function getModuleConfig(string $slug, string $connection = 'landlord'): object
    {
        $module = DB::connection($connection)
            ->table('modules')
            ->where('slug', $slug)
            ->whereNull('deleted_at')
            ->first();

        if (!$module) {
            abort(404, "Módulo '{$slug}' não encontrado.");
        }

        return $module;
    }

    /**
     * Busca os fields com UI para montar grid e formulários.
     */
    protected function getFieldsWithUi(int $moduleId, string $connection = 'landlord'): array
    {
        return DB::connection($connection)
            ->table('module_fields')
            ->join('module_fields_ui', 'module_fields.id', '=', 'module_fields_ui.module_field_id')
            ->where('module_fields.module_id', $moduleId)
            ->where('module_fields.status', true)
            ->where('module_fields_ui.status', true)
            ->whereNull('module_fields.deleted_at')
            ->whereNull('module_fields_ui.deleted_at')
            ->orderBy('module_fields_ui.order')
            ->select(
                'module_fields.*',
                'module_fields_ui.component',
                'module_fields_ui.options as ui_options',
                'module_fields_ui.placeholder',
                'module_fields_ui.mask',
                'module_fields_ui.icon as ui_icon',
                'module_fields_ui.tooltip',
                'module_fields_ui.tooltip_direction',
                'module_fields_ui.grid_col',
                'module_fields_ui.visible_index',
                'module_fields_ui.visible_show',
                'module_fields_ui.visible_create',
                'module_fields_ui.visible_edit',
                'module_fields_ui.width_index',
                'module_fields_ui.grid_template',
                'module_fields_ui.grid_link',
                'module_fields_ui.grid_actions',
                'module_fields_ui.searchable',
                'module_fields_ui.sortable',
                'module_fields_ui.order as ui_order'
            )
            ->get()
            ->map(function ($field) {
                $field->ui_options = $field->ui_options ? json_decode($field->ui_options, true) : null;
                $field->grid_actions = $field->grid_actions ? json_decode($field->grid_actions, true) : null;
                return $field;
            })
            ->toArray();
    }

    /**
     * Resolve o service (específico ou DynamicService).
     */
    protected function resolveService(string $slug, string $connection = 'landlord'): mixed
    {
        $module = $this->getModuleConfig($slug, $connection);

        if ($module->service && class_exists($module->service)) {
            return new ($module->service)();
        }

        $model = DynamicModel::forModule($slug, $connection);
        return new DynamicService($model);
    }

    /**
     * GET /{module} — Listagem
     */
    public function index(Request $request, string $module)
    {
        $connection = $this->getConnection();
        $config = $this->getModuleConfig($module, $connection);
        $fieldsWithUi = $this->getFieldsWithUi($config->id, $connection);
        $service = $this->resolveService($module, $connection);

        // Verificar se a tabela física existe
        $tableName = $config->slug;
        $tableExists = \Illuminate\Support\Facades\Schema::connection($connection)->hasTable($tableName);

        if (!$tableExists) {
            return view('landlord.pages.dynamic.coming-soon', compact('config', 'module'));
        }

        // Filtrar campos visíveis no index
        $indexFields = array_filter($fieldsWithUi, fn ($f) => $f->visible_index);

        $params = [
            'sort_by'       => $request->get('sort_by', $config->default_sort_field),
            'sort_direction' => $request->get('sort_direction', $config->default_sort_direction),
            'per_page'      => $request->get('per_page', $config->per_page),
            'quick_search'  => $request->get('quick_search'),
            'with_trashed'  => $request->get('with_trashed'),
        ];

        $records = $service->index($params);

        // AJAX: retorna apenas o tbody (partial)
        if ($request->ajax()) {
            return view('landlord.pages.dynamic._tbody', compact(
                'config', 'indexFields', 'records', 'module'
            ));
        }

        return view('landlord.pages.dynamic.index', compact(
            'config', 'indexFields', 'fieldsWithUi', 'records', 'module'
        ));
    }

    /**
     * GET /{module}/{code} — Detalhe
     */
    public function show(Request $request, string $module, string $code)
    {
        $connection = $this->getConnection();
        $config = $this->getModuleConfig($module, $connection);
        $fieldsWithUi = $this->getFieldsWithUi($config->id, $connection);
        $service = $this->resolveService($module, $connection);

        // Verificar se a tabela física existe
        $tableName = $config->slug;
        $tableExists = \Illuminate\Support\Facades\Schema::connection($connection)->hasTable($tableName);

        if (!$tableExists) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Módulo não possui tabela física.',
                ], 404);
            }
            return redirect()->to(url('/' . $module));
        }

        $id = decodeId($code);
        $record = $service->find($id);

        // Se for requisição AJAX, retornar JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $record,
            ]);
        }

        // Se o módulo tem view_show customizada
        if ($config->view_show) {
            // Buscar contagens extras para views customizadas (ex: módulo "Módulos")
            $fieldsCount = \DB::connection($connection)->table('module_fields')->where('module_id', $record->id)->count();
            $submodulesCount = \DB::connection($connection)->table('module_submodules')->where('module_id', $record->id)->count();
            $seedsCount = \DB::connection($connection)->table('module_seeds')->where('module_id', $record->id)->count();

            // Renomear $record para $module (nome esperado pela view customizada)
            $module = $record;

            return view($config->view_show, compact('module', 'fieldsCount', 'submodulesCount', 'seedsCount'));
        }

        // View genérica padrão
        // Filtrar campos visíveis no show
        $showFields = array_filter($fieldsWithUi, fn ($f) => $f->visible_show);

        return view('landlord.pages.dynamic.show', compact(
            'config', 'showFields', 'fieldsWithUi', 'record', 'module', 'code'
        ));
    }

    /**
     * POST /{module} — Criar
     */
    public function store(Request $request, string $module)
    {
        $connection = $this->getConnection();
        $validated = $this->validateDynamic($request, $module, $connection);
        $service = $this->resolveService($module, $connection);

        $record = $service->create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Registro criado com sucesso.',
                'data'    => $record,
            ]);
        }

        return redirect()->to(url('/' . $module))
            ->with('success', 'Registro criado com sucesso.');
    }

    /**
     * PUT /{module}/{code} — Atualizar
     */
    public function update(Request $request, string $module, string $code)
    {
        $connection = $this->getConnection();
        $validated = $this->validateDynamic($request, $module, $connection);
        $service = $this->resolveService($module, $connection);

        $id = decodeId($code);
        $record = $service->update($id, $validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Registro atualizado com sucesso.',
                'data'    => $record,
            ]);
        }

        return redirect()->to(url('/' . $module))
            ->with('success', 'Registro atualizado com sucesso.');
    }

    /**
     * DELETE /{module}/{code} — Soft delete
     */
    public function destroy(Request $request, string $module, string $code)
    {
        $connection = $this->getConnection();
        $service = $this->resolveService($module, $connection);

        $id = decodeId($code);
        $service->destroy($id);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Registro removido com sucesso.',
            ]);
        }

        return redirect()->to(url('/' . $module))
            ->with('success', 'Registro removido com sucesso.');
    }

    /**
     * PATCH /{module}/{code}/restore — Restaurar
     */
    public function restore(Request $request, string $module, string $code)
    {
        $connection = $this->getConnection();
        $service = $this->resolveService($module, $connection);

        $id = decodeId($code);
        $service->restore($id);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Registro restaurado com sucesso.',
            ]);
        }

        return redirect()->to(url('/' . $module))
            ->with('success', 'Registro restaurado com sucesso.');
    }

    /**
     * POST /{module}/reorder — Reordenar
     */
    public function reorder(Request $request, string $module)
    {
        $connection = $this->getConnection();
        $service = $this->resolveService($module, $connection);

        $service->reorder($request->get('items', []));

        return response()->json([
            'success' => true,
            'message' => 'Registros reordenados com sucesso.',
        ]);
    }

    /**
     * Valida request com DynamicRequest.
     */
    protected function validateDynamic(Request $request, string $slug, string $connection): array
    {
        $dynamicRequest = new DynamicRequest();
        $dynamicRequest->setModuleSlug($slug)->setConnectionName($connection);

        return $request->validate(
            $dynamicRequest->rules(),
            [],
            $dynamicRequest->attributes()
        );
    }

    /**
     * Determina a conexão com base no contexto.
     */
    protected function getConnection(): string
    {
        return 'landlord';
    }
}
