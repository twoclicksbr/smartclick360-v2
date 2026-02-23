<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Module;
use App\Models\Landlord\ModuleField;
use App\Models\Landlord\ModuleFieldUi;
use App\Models\Landlord\ModuleSeed;
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

        // Buscar submódulos disponíveis e vinculados
        $allSubmodules = \App\Models\Landlord\Module::where('type', 'submodule')
            ->where('status', true)
            ->orderBy('name')
            ->get();

        $linkedSubmodules = \App\Models\Landlord\ModuleSubmodule::where('module_id', $module->id)
            ->pluck('submodule_id')
            ->toArray();

        // Gerar dados fake para preview da grid
        $fakeData = $this->generateFakeData($fields);

        // Buscar seeds do módulo
        $seeds = \App\Models\Landlord\ModuleSeed::where('module_id', $module->id)
            ->orderBy('order')
            ->get();

        return view('landlord.pages.modules.show', compact('module', 'fields', 'fieldsCount', 'submodulesCount', 'seedsCount', 'allSubmodules', 'linkedSubmodules', 'fakeData', 'seeds'));
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

        // Auto-criar registro em module_fields_ui com defaults
        $componentMap = [
            'STRING'    => 'input',
            'TEXT'      => 'textarea',
            'INTEGER'   => 'input',
            'BIGINT'    => $field->fk_table ? 'select_module' : 'input',
            'DECIMAL'   => 'input',
            'BOOLEAN'   => 'switch',
            'DATE'      => 'date',
            'TIMESTAMP' => 'input',
        ];

        $isSystem = !$field->is_custom;
        $component = $componentMap[$field->type] ?? 'input';

        \App\Models\Landlord\ModuleFieldUi::create([
            'module_field_id'  => $field->id,
            'component'        => $component,
            'grid_col'         => 'col-md-12',
            'visible_index'    => $field->is_custom,
            'visible_show'     => $field->is_custom,
            'visible_create'   => $field->is_custom,
            'visible_edit'     => $field->is_custom,
            'searchable'       => false,
            'sortable'         => false,
            'order'            => $field->order,
            'status'           => true,
            'origin'           => $field->origin,
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
     * GET /modules/{code}/fields-list — Lista campos do módulo (JSON)
     */
    public function getFieldsList($code)
    {
        $moduleId = decodeId($code);
        $module = \App\Models\Landlord\Module::findOrFail($moduleId);

        $fields = \App\Models\Landlord\ModuleField::where('module_id', $module->id)
            ->select('id', 'name', 'label')
            ->orderBy('order')
            ->get();

        return response()->json($fields);
    }

    /**
     * PUT /modules/{code}/fields/{fieldCode}/grid — Atualizar configuração de grid do campo
     */
    public function updateFieldGrid(Request $request, $code, $fieldCode)
    {
        $moduleId = decodeId($code);
        $fieldId = decodeId($fieldCode);

        $module = \App\Models\Landlord\Module::findOrFail($moduleId);
        $field = \App\Models\Landlord\ModuleField::where('id', $fieldId)
            ->where('module_id', $module->id)
            ->firstOrFail();

        $ui = \App\Models\Landlord\ModuleFieldUi::firstOrCreate(
            ['module_field_id' => $field->id],
            [
                'component'          => 'input',
                'grid_col'           => 'col-md-12',
                'visible_index'      => true,
                'visible_create'     => true,
                'visible_edit'       => true,
                'visible_show'       => true,
                'searchable'         => true,
                'sortable'           => true,
                'order'              => $field->order,
                'status'             => true,
            ]
        );

        // Atualiza module_fields_ui (só campos enviados)
        $uiData = [];

        // Campos do thead
        if ($request->has('grid_label'))    $uiData['grid_label'] = $request->input('grid_label') ?: null;
        if ($request->has('visible_index')) $uiData['visible_index'] = $request->boolean('visible_index');
        if ($request->has('searchable'))    $uiData['searchable'] = $request->boolean('searchable');
        if ($request->has('sortable'))      $uiData['sortable'] = $request->boolean('sortable');
        if ($request->has('width_index'))   $uiData['width_index'] = $request->input('width_index') ?: null;

        // Campos do tbody
        if ($request->has('grid_template')) $uiData['grid_template'] = $request->input('grid_template') ?: null;
        if ($request->has('grid_link'))     $uiData['grid_link'] = $request->input('grid_link') ?: null;
        if ($request->has('grid_actions'))  $uiData['grid_actions'] = $request->input('grid_actions') ? json_decode($request->input('grid_actions'), true) : null;
        if ($request->has('options'))       $uiData['options'] = $request->input('options') ? json_decode($request->input('options'), true) : null;

        $ui->update($uiData);

        // Se searchable = true, auto-seta index na module_fields
        if ($request->boolean('searchable') && !$field->index) {
            $field->update(['index' => true]);
        }

        // Se default_sort marcado, atualiza na tabela modules
        if ($request->boolean('default_sort')) {
            $module->update([
                'default_sort_field'     => $field->name,
                'default_sort_direction' => $request->input('sort_direction', 'asc'),
            ]);
        } elseif ($module->default_sort_field === $field->name) {
            // Se desmarcou o default_sort deste campo, volta para 'order'
            $module->update([
                'default_sort_field'     => 'order',
                'default_sort_direction' => 'asc',
            ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Configuração da coluna salva com sucesso',
                'data'    => [
                    'field_name'           => $field->name,
                    'visible_index'        => $ui->visible_index,
                    'searchable'           => $ui->searchable,
                    'sortable'             => $ui->sortable,
                    'width_index'          => $ui->width_index,
                    'default_sort_field'   => $module->default_sort_field,
                    'default_sort_direction'=> $module->default_sort_direction,
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Configuração salva');
    }

    /**
     * Aba Form — Atualiza configuração visual de um campo
     */
    public function updateFieldForm(Request $request, string $code, string $fieldCode)
    {
        $moduleId = decodeId($code);
        $fieldId = decodeId($fieldCode);

        $module = Module::findOrFail($moduleId);
        $field = ModuleField::where('id', $fieldId)->where('module_id', $module->id)->firstOrFail();
        $ui = ModuleFieldUi::firstOrCreate(
            ['module_field_id' => $field->id],
            [
                'component'          => 'input',
                'grid_col'           => 'col-md-12',
                'visible_index'      => true,
                'visible_create'     => true,
                'visible_edit'       => true,
                'visible_show'       => true,
                'searchable'         => true,
                'sortable'           => true,
                'order'              => $field->order,
                'status'             => true,
            ]
        );

        // Atualizar module_fields_ui (campos de apresentação)
        $uiData = [];
        if ($request->has('component'))         $uiData['component'] = $request->input('component');
        if ($request->has('grid_col'))           $uiData['grid_col'] = $request->input('grid_col');
        if ($request->has('icon'))               $uiData['icon'] = $request->input('icon') ?: null;
        if ($request->has('placeholder'))        $uiData['placeholder'] = $request->input('placeholder') ?: null;
        if ($request->has('mask'))               $uiData['mask'] = $request->input('mask') ?: null;
        if ($request->has('tooltip'))            $uiData['tooltip'] = $request->input('tooltip') ?: null;
        if ($request->has('tooltip_direction'))  $uiData['tooltip_direction'] = $request->input('tooltip_direction') ?: 'top';
        if ($request->has('visible_create'))     $uiData['visible_create'] = $request->boolean('visible_create');
        if ($request->has('visible_edit'))       $uiData['visible_edit'] = $request->boolean('visible_edit');
        if ($request->has('visible_show'))       $uiData['visible_show'] = $request->boolean('visible_show');

        // Options: recebe string JSON, decodifica para salvar como JSON no banco
        if ($request->has('options')) {
            $optionsRaw = $request->input('options');
            if ($optionsRaw && is_string($optionsRaw)) {
                $decoded = json_decode($optionsRaw, true);
                $uiData['options'] = $decoded ?: null;
            } else {
                $uiData['options'] = null;
            }
        }

        if (!empty($uiData)) {
            $ui->update($uiData);
        }

        // Atualizar module_fields (dados de negócio condicionais)
        $fieldData = [];

        // FK: se component = select_module, salvar fk_table, fk_column, fk_label
        $component = $request->input('component', $ui->component);
        if ($component === 'select_module') {
            if ($request->has('fk_table'))  $fieldData['fk_table'] = $request->input('fk_table') ?: null;
            if ($request->has('fk_column')) $fieldData['fk_column'] = $request->input('fk_column') ?: null;
            if ($request->has('fk_label'))  $fieldData['fk_label'] = $request->input('fk_label') ?: null;
        }

        // Unique: se campo tem unique=true, salvar unique_table e unique_column
        if ($field->unique) {
            if ($request->has('unique_table'))  $fieldData['unique_table'] = $request->input('unique_table') ?: null;
            if ($request->has('unique_column')) $fieldData['unique_column'] = $request->input('unique_column') ?: null;
        }

        if (!empty($fieldData)) {
            $field->update($fieldData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Configuração do campo atualizada com sucesso.',
            'data' => [
                'ui' => $ui->fresh(),
                'field' => $field->fresh(),
            ]
        ]);
    }

    /**
     * Aba Seeds — Lista seeds do módulo
     */
    public function indexSeeds(string $code)
    {
        $moduleId = decodeId($code);
        $module = Module::findOrFail($moduleId);

        $seeds = \App\Models\Landlord\ModuleSeed::where('module_id', $module->id)
            ->orderBy('order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $seeds
        ]);
    }

    /**
     * Aba Seeds — Cria novo seed
     */
    public function storeSeed(Request $request, string $code)
    {
        $moduleId = decodeId($code);
        $module = Module::findOrFail($moduleId);

        // Obter o maior order atual
        $maxOrder = \App\Models\Landlord\ModuleSeed::where('module_id', $module->id)->max('order') ?? 0;

        $seed = \App\Models\Landlord\ModuleSeed::create([
            'module_id' => $module->id,
            'data'      => $request->input('data', []),
            'order'     => $maxOrder + 1,
            'status'    => true,
            'origin'    => 'custom',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Seed criado com sucesso.',
            'data'    => array_merge($seed->toArray(), ['encoded_id' => encodeId($seed->id)])
        ], 201);
    }

    /**
     * Aba Seeds — Atualiza seed existente
     */
    public function updateSeed(Request $request, string $code, string $seedCode)
    {
        $moduleId = decodeId($code);
        $seedId = decodeId($seedCode);

        $module = Module::findOrFail($moduleId);
        $seed = \App\Models\Landlord\ModuleSeed::where('id', $seedId)
            ->where('module_id', $module->id)
            ->firstOrFail();

        $seed->update([
            'data' => $request->input('data', []),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Seed atualizado com sucesso.',
            'data'    => array_merge($seed->fresh()->toArray(), ['encoded_id' => encodeId($seed->id)])
        ]);
    }

    /**
     * Aba Seeds — Remove seed
     */
    public function destroySeed(string $code, string $seedCode)
    {
        $moduleId = decodeId($code);
        $seedId = decodeId($seedCode);

        $module = Module::findOrFail($moduleId);
        $seed = \App\Models\Landlord\ModuleSeed::where('id', $seedId)
            ->where('module_id', $module->id)
            ->firstOrFail();

        $seed->delete();

        return response()->json([
            'success' => true,
            'message' => 'Seed removido com sucesso.'
        ]);
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

    /**
     * Gera dados fake para preview da grid
     */
    private function generateFakeData($fields): array
    {
        $fakeData = [];

        foreach ($fields as $field) {
            $fakeData[$field->name] = match($field->type) {
                'STRING' => match(true) {
                    str_contains($field->name, 'email') => ['joao@email.com', 'maria@email.com', 'pedro@email.com'],
                    str_contains($field->name, 'name') || str_contains($field->name, 'nome') => ['João Silva', 'Maria Santos', 'Pedro Lima'],
                    str_contains($field->name, 'slug') => ['joao-silva', 'maria-santos', 'pedro-lima'],
                    str_contains($field->name, 'phone') || str_contains($field->name, 'telefone') => ['11999990001', '11999990002', '11999990003'],
                    default => ['Texto exemplo 1', 'Texto exemplo 2', 'Texto exemplo 3'],
                },
                'TEXT' => ['Lorem ipsum dolor sit amet...', 'Consectetur adipiscing elit...', 'Sed do eiusmod tempor...'],
                'INTEGER' => match(true) {
                    $field->name === 'order' => [1, 2, 3],
                    default => [10, 25, 42],
                },
                'BIGINT' => match(true) {
                    $field->name === 'id' => [1, 2, 3],
                    $field->fk_table !== null => $this->getFkFakeLabels($field, 3),
                    default => [100, 200, 300],
                },
                'DECIMAL' => ['149,90', '299,00', '59,50'],
                'BOOLEAN' => [true, false, true],
                'DATE' => ['15/01/2026', '03/02/2026', '20/03/2026'],
                'TIMESTAMP' => match(true) {
                    str_contains($field->name, 'deleted') => [null, null, '10/03/2026 08:00'],
                    default => ['15/01/2026 14:30', '03/02/2026 09:15', '20/03/2026 17:45'],
                },
                default => ['—', '—', '—'],
            };
        }

        return $fakeData;
    }

    /**
     * Busca labels de FK para dados fake
     */
    private function getFkFakeLabels($field, int $count): array
    {
        try {
            $labels = \Illuminate\Support\Facades\DB::connection('landlord')
                ->table($field->fk_table)
                ->whereNull('deleted_at')
                ->where('status', true)
                ->orderBy('id')
                ->limit($count)
                ->pluck($field->fk_label ?? 'name')
                ->toArray();

            // Se não encontrou registros suficientes, completa com placeholders
            while (count($labels) < $count) {
                $labels[] = '(sem registro)';
            }

            return $labels;
        } catch (\Exception $e) {
            return array_fill(0, $count, '(FK: ' . $field->fk_table . ')');
        }
    }
}
