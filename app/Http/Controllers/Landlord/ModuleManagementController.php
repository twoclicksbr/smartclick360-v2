<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModuleManagementController extends Controller
{
    /**
     * POST /modules — Criar módulo
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:modules,slug',
            'type' => 'required|string|in:module,submodule',
            'scope' => 'required|string|in:tenant,landlord',
            'icon' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:255',
            'service' => 'nullable|string|max:255',
            'controller' => 'nullable|string|max:255',
        ]);

        // Criar módulo
        $module = Module::create(array_merge($validated, [
            'show_drag' => true,
            'show_checkbox' => true,
            'show_actions' => true,
            'default_sort_field' => 'id',
            'default_sort_direction' => 'asc',
            'per_page' => 25,
            'after_store' => 'index',
            'after_update' => 'index',
            'after_restore' => 'edit',
            'default_checked' => false,
            'origin' => 'custom',
            'order' => 0,
            'status' => true,
        ]));

        // Criar campos system automaticamente
        $systemFields = [
            ['name' => 'id',         'label' => 'ID',         'type' => 'bigInteger', 'nullable' => false, 'required' => false, 'unique' => false, 'index' => false, 'order' => 1],
            ['name' => 'order',      'label' => 'Ordem',      'type' => 'integer',    'nullable' => false, 'required' => false, 'unique' => false, 'index' => false, 'order' => 2, 'default' => '0'],
            ['name' => 'status',     'label' => 'Status',     'type' => 'boolean',    'nullable' => false, 'required' => false, 'unique' => false, 'index' => false, 'order' => 3, 'default' => 'true'],
            ['name' => 'created_at', 'label' => 'Criado em',  'type' => 'timestamp',  'nullable' => true,  'required' => false, 'unique' => false, 'index' => false, 'order' => 4],
            ['name' => 'updated_at', 'label' => 'Atualizado em','type' => 'timestamp','nullable' => true,  'required' => false, 'unique' => false, 'index' => false, 'order' => 5],
            ['name' => 'deleted_at', 'label' => 'Deletado em','type' => 'timestamp',  'nullable' => true,  'required' => false, 'unique' => false, 'index' => false, 'order' => 6],
        ];

        foreach ($systemFields as $sf) {
            \App\Models\Landlord\ModuleField::create(array_merge($sf, [
                'module_id' => $module->id,
                'main'      => ($sf['name'] === 'id') ? true : false,
                'is_custom' => false,
                'origin'    => 'system',
                'status'    => true,
            ]));
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Módulo criado com sucesso.',
                'data' => $module,
            ]);
        }

        return redirect()->route('landlord.modules.show', ['code' => encodeId($module->id)])
            ->with('success', 'Módulo criado com sucesso.');
    }

    /**
     * GET /modules/{code} — Detalhe do módulo
     */
    public function show(string $code)
    {
        $id = decodeId($code);
        $module = Module::findOrFail($id);

        // Buscar fields com UI
        $fields = \App\Models\Landlord\ModuleField::withTrashed()
            ->where('module_id', $module->id)
            ->with(['ui' => function ($query) {
                $query->withTrashed();
            }])
            ->orderBy('order')
            ->get();
        $fieldsCount = $fields->count();

        // Buscar contagens extras
        $submodulesCount = DB::connection('landlord')->table('module_submodules')->where('module_id', $module->id)->count();
        $seedsCount = DB::connection('landlord')->table('module_seeds')->where('module_id', $module->id)->count();

        return view('landlord.pages.modules.show', compact('module', 'fields', 'fieldsCount', 'submodulesCount', 'seedsCount'));
    }

    /**
     * PUT /modules/{code} — Atualizar módulo
     */
    public function update(Request $request, string $code)
    {
        $id = decodeId($code);
        $module = Module::findOrFail($id);

        // Validação manual de todos os 28 campos
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:modules,slug,' . $module->id,
            'type' => 'required|string|in:module,submodule',
            'scope' => 'required|string|in:tenant,landlord',
            'icon' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:255',
            'service' => 'nullable|string|max:255',
            'controller' => 'nullable|string|max:255',
            'show_drag' => 'nullable|boolean',
            'show_checkbox' => 'nullable|boolean',
            'show_actions' => 'nullable|boolean',
            'default_sort_field' => 'nullable|string|max:255',
            'default_sort_direction' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:1000',
            'description_index' => 'nullable|string|max:255',
            'description_show' => 'nullable|string|max:255',
            'description_create' => 'nullable|string|max:255',
            'description_edit' => 'nullable|string|max:255',
            'view_index' => 'nullable|string|max:255',
            'view_show' => 'nullable|string|max:255',
            'view_modal' => 'nullable|string|max:255',
            'after_store' => 'nullable|string|in:index,show,edit',
            'after_update' => 'nullable|string|in:index,show,edit',
            'after_restore' => 'nullable|string|in:index,show,edit',
            'default_checked' => 'nullable|boolean',
            'origin' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
            'status' => 'nullable|boolean',
        ]);

        // Tratar campos boolean (checkboxes não enviam valor quando desmarcados)
        $booleanFields = ['show_drag', 'show_checkbox', 'show_actions', 'default_checked', 'status'];
        foreach ($booleanFields as $field) {
            $validated[$field] = $request->has($field) ? (bool) $request->input($field) : false;
        }

        // Atualizar no banco
        $module->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Registro atualizado com sucesso.',
                'data' => $module,
            ]);
        }

        return redirect()->back()
            ->with('success', 'Registro atualizado com sucesso.');
    }

    /**
     * POST /modules/{code}/fields — Criar campo
     */
    public function storeField(Request $request, string $code)
    {
        $moduleId = decodeId($code);
        $module = Module::findOrFail($moduleId);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'type'     => 'required|string|max:50',
            'length'   => 'nullable|string|max:20',
            'default'  => 'nullable|string|max:255',
            'required' => 'nullable',
            'nullable' => 'nullable',
            'unique'   => 'nullable',
            'index'    => 'nullable',
        ]);

        // Parse length/precision (ex: "10,2" → length=10, precision=2)
        $length = null;
        $precision = null;
        if (!empty($validated['length'])) {
            if (str_contains($validated['length'], ',')) {
                $parts = explode(',', $validated['length']);
                $length = (int) trim($parts[0]);
                $precision = (int) trim($parts[1]);
            } else {
                $length = (int) $validated['length'];
            }
        }

        // Inferir FK automaticamente (brand_id → fk_table=brands, fk_column=id)
        $fkTable = null;
        $fkColumn = null;
        if ($validated['type'] === 'foreignId' && str_ends_with($validated['name'], '_id')) {
            $fkTable = \Illuminate\Support\Str::plural(str_replace('_id', '', $validated['name']));
            $fkColumn = 'id';
        }

        $field = \App\Models\Landlord\ModuleField::create([
            'module_id' => $module->id,
            'name'      => $validated['name'],
            'label'     => $validated['name'],
            'type'      => $validated['type'],
            'length'    => $length,
            'precision' => $precision,
            'default'   => $validated['default'] ?? null,
            'nullable'  => (bool) ($validated['nullable'] ?? false),
            'required'  => (bool) ($validated['required'] ?? false),
            'unique'    => (bool) ($validated['unique'] ?? false),
            'index'     => (bool) ($validated['index'] ?? false),
            'fk_table'  => $fkTable,
            'fk_column' => $fkColumn,
            'main'      => false,
            'is_custom' => true,
            'origin'    => 'custom',
            'order'     => 2, // temporário, será recalculado
            'status'    => true,
        ]);

        // Reordenar: id(1) → customs(2,3,4...) → system restantes(N+1,N+2...)
        $this->reorderModuleFields($module->id);

        return response()->json(['success' => true, 'message' => 'Campo criado com sucesso', 'data' => $field]);
    }

    /**
     * GET /modules/{code}/fields/{fcode} — Dados de um campo (JSON para edição)
     */
    public function showField(Request $request, string $code, string $fcode)
    {
        $moduleId = decodeId($code);
        $fieldId = decodeId($fcode);
        $module = Module::findOrFail($moduleId);
        $field = \App\Models\Landlord\ModuleField::where('module_id', $module->id)
            ->with('ui')
            ->findOrFail($fieldId);

        return response()->json(['success' => true, 'field' => $field]);
    }

    /**
     * PUT /modules/{code}/fields/{fcode} — Atualizar campo
     */
    public function updateField(Request $request, string $code, string $fcode)
    {
        $moduleId = decodeId($code);
        $fieldId = decodeId($fcode);
        $module = Module::findOrFail($moduleId);
        $field = \App\Models\Landlord\ModuleField::where('module_id', $module->id)->findOrFail($fieldId);

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'label'     => 'nullable|string|max:255',
            'type'      => 'required|string|max:50',
            'length'    => 'nullable|string|max:20',
            'default'   => 'nullable|string|max:255',
            'required'  => 'nullable',
            'nullable'  => 'nullable',
            'unique'    => 'nullable',
            'index'     => 'nullable',
            'component' => 'nullable|string|max:50',
        ]);

        // Parse length/precision
        $length = null;
        $precision = null;
        if (!empty($validated['length'])) {
            if (str_contains($validated['length'], ',')) {
                $parts = explode(',', $validated['length']);
                $length = (int) trim($parts[0]);
                $precision = (int) trim($parts[1]);
            } else {
                $length = (int) $validated['length'];
            }
        }

        // Inferir FK
        $fkTable = null;
        $fkColumn = null;
        if ($validated['type'] === 'foreignId' && str_ends_with($validated['name'], '_id')) {
            $fkTable = \Illuminate\Support\Str::plural(str_replace('_id', '', $validated['name']));
            $fkColumn = 'id';
        }

        // Atualizar field
        $field->update([
            'main' => $request->boolean('main'),
            'is_custom' => $request->boolean('is_custom'),
            'icon' => $request->input('icon'),
            'name' => $request->input('name'),
            'label' => $validated['label'] ?? $field->label ?? $validated['name'],
            'type' => $request->input('type'),
            'length' => $length,
            'precision' => $precision,
            'default' => $request->input('default'),
            'nullable' => $request->boolean('nullable'),
            'required' => $request->boolean('required'),
            'unique' => $request->boolean('unique'),
            'index' => $request->boolean('index'),
            'fk_table' => $fkTable,
            'fk_column' => $fkColumn,
            'fk_label' => $request->input('fk_label'),
            'auto_from' => $request->input('auto_from'),
            'auto_type' => $request->input('auto_type'),
            'min' => $request->input('min'),
            'max' => $request->input('max'),
            'status' => $request->boolean('status', true),
            'origin' => $request->input('origin', 'custom'),
        ]);

        // Atualizar ou criar UI
        $ui = $field->ui ?? new \App\Models\Landlord\ModuleFieldUi();
        $ui->module_field_id = $field->id;
        $ui->component = $validated['component'] ?? $ui->component ?? 'input';
        $ui->options = $request->input('options') ? json_decode($request->input('options'), true) : null;
        $ui->placeholder = $request->input('placeholder');
        $ui->mask = $request->input('mask');
        $ui->icon = $request->input('ui_icon');
        $ui->tooltip = $request->input('tooltip');
        $ui->tooltip_direction = $request->input('tooltip_direction', 'top');
        $ui->grid_col = $request->input('grid_col', 'col-md-12');
        $ui->visible_index = $request->boolean('visible_index');
        $ui->visible_show = $request->boolean('visible_show');
        $ui->visible_create = $request->boolean('visible_create', true);
        $ui->visible_edit = $request->boolean('visible_edit', true);
        $ui->width_index = $request->input('width_index');
        $ui->grid_template = $request->input('grid_template');
        $ui->grid_link = $request->input('grid_link');
        $ui->searchable = $request->boolean('searchable');
        $ui->sortable = $request->boolean('sortable');
        $ui->status = $request->boolean('status', true);
        $ui->origin = $request->input('origin', 'custom');
        $ui->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Campo atualizado com sucesso!', 'field' => $field->load('ui')]);
        }

        return redirect()->back()->with('success', 'Campo atualizado com sucesso!');
    }

    /**
     * DELETE /modules/{code}/fields/{fcode} — Soft delete campo
     */
    public function destroyField(Request $request, string $code, string $fcode)
    {
        $moduleId = decodeId($code);
        $fieldId = decodeId($fcode);
        $module = Module::findOrFail($moduleId);
        $field = \App\Models\Landlord\ModuleField::where('module_id', $module->id)->findOrFail($fieldId);

        $field->delete(); // soft delete

        // Soft delete na UI também
        if ($field->ui) {
            $field->ui->delete();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Campo excluído com sucesso!']);
        }

        return redirect()->back()->with('success', 'Campo excluído com sucesso!');
    }

    /**
     * PATCH /modules/{code}/fields/{fcode}/restore — Restaurar campo
     */
    public function restoreField(Request $request, string $code, string $fcode)
    {
        $moduleId = decodeId($code);
        $fieldId = decodeId($fcode);
        $module = Module::findOrFail($moduleId);
        $field = \App\Models\Landlord\ModuleField::withTrashed()->where('module_id', $module->id)->findOrFail($fieldId);

        $field->restore();

        // Restaurar UI também
        $ui = \App\Models\Landlord\ModuleFieldUi::withTrashed()->where('module_field_id', $field->id)->first();
        if ($ui) {
            $ui->restore();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Campo restaurado com sucesso!']);
        }

        return redirect()->back()->with('success', 'Campo restaurado com sucesso!');
    }

    /**
     * POST /modules/{code}/fields/reorder — Reordenar campos
     */
    public function reorderFields(Request $request, string $code)
    {
        $moduleId = decodeId($code);
        $module = Module::findOrFail($moduleId);

        $request->validate(['ids' => 'required|array']);

        // Atualizar ordem temporária dos campos custom conforme arrastados pelo usuário
        foreach ($request->input('ids') as $index => $encodedId) {
            $fieldId = decodeId($encodedId);
            DB::connection('landlord')
                ->table('module_fields')
                ->where('id', $fieldId)
                ->where('module_id', $module->id)
                ->update(['order' => $index + 100]); // offset temporário para evitar conflitos
        }

        // Reordenar tudo no padrão correto: id(1) → customs(2,3...) → system rest(N+1...)
        $this->reorderModuleFields($module->id);

        return response()->json(['success' => true, 'message' => 'Ordem atualizada!']);
    }

    /**
     * Reordena campos: id(1) → customs(2,3,4...) → system restantes(N+1,N+2...)
     */
    private function reorderModuleFields(int $moduleId): void
    {
        $systemOrder = ['id', 'order', 'status', 'created_at', 'updated_at', 'deleted_at'];

        // Buscar todos os campos ativos do módulo
        $fields = \App\Models\Landlord\ModuleField::where('module_id', $moduleId)
            ->whereNull('deleted_at')
            ->get();

        // Separar: id, customs, system restantes
        $idField = $fields->where('name', 'id')->where('origin', 'system')->first();
        $customFields = $fields->where('origin', 'custom')->sortBy('order');
        $systemRest = $fields->where('origin', 'system')
            ->where('name', '!=', 'id')
            ->sortBy(function ($f) use ($systemOrder) {
                return array_search($f->name, $systemOrder);
            });

        // Reordenar sequencialmente
        $order = 1;

        // 1. id sempre primeiro
        if ($idField) {
            $idField->update(['order' => $order++]);
        }

        // 2. customs no meio
        foreach ($customFields as $field) {
            $field->update(['order' => $order++]);
        }

        // 3. system restantes no final
        foreach ($systemRest as $field) {
            $field->update(['order' => $order++]);
        }
    }
}
