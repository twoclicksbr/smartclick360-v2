<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\VariationType;
use Illuminate\Http\Request;

class VariationTypesController extends Controller
{
    public function index(Request $request, string $slug, string $module)
    {
        $tenant = $request->tenant;
        $variationTypes = VariationType::orderBy('order')->orderBy('id')->get();

        return view('tenant.pages.variation-types.index', compact('tenant', 'variationTypes'));
    }

    public function create(string $slug, string $module)
    {
        //
    }

    public function store(Request $request, string $slug, string $module)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->status : true;
        $validated['order'] = VariationType::max('order') + 1;

        VariationType::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tipo de variação cadastrado com sucesso!'
        ]);
    }

    public function show(string $slug, string $module, string $code)
    {
        //
    }

    public function edit(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $variationType = VariationType::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $variationType
        ]);
    }

    public function update(Request $request, string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $variationType = VariationType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->status : $variationType->status;

        $variationType->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tipo de variação atualizado com sucesso!'
        ]);
    }

    public function destroy(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $variationType = VariationType::findOrFail($id);
        $variationType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tipo de variação excluído com sucesso!'
        ]);
    }

    public function restore(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $variationType = VariationType::withTrashed()->findOrFail($id);
        $variationType->restore();

        return response()->json([
            'success' => true,
            'message' => 'Tipo de variação restaurado com sucesso!'
        ]);
    }

    public function reorder(Request $request, string $slug, string $module)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:variation_types,id',
        ]);

        foreach ($validated['order'] as $index => $id) {
            VariationType::where('id', $id)->update(['order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ordem atualizada com sucesso!'
        ]);
    }
}
