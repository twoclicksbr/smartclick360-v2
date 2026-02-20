<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModuleManagementController extends Controller
{
    /**
     * GET /modules/{code} — Detalhe do módulo
     */
    public function show(string $code)
    {
        $id = decodeId($code);
        $module = Module::findOrFail($id);

        // Buscar contagens extras
        $fieldsCount = DB::connection('landlord')->table('module_fields')->where('module_id', $module->id)->count();
        $submodulesCount = DB::connection('landlord')->table('module_submodules')->where('module_id', $module->id)->count();
        $seedsCount = DB::connection('landlord')->table('module_seeds')->where('module_id', $module->id)->count();

        return view('landlord.pages.modules.show', compact('module', 'fieldsCount', 'submodulesCount', 'seedsCount'));
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
}
