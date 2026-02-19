<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\VariationOption;
use App\Models\Tenant\VariationType;
use Illuminate\Http\Request;

class VariationOptionsController extends Controller
{
    public function index(Request $request, string $slug, string $module)
    {
        $tenant = $request->tenant;
        $variationOptions = VariationOption::with('variationType')
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $variationTypes = VariationType::where('status', true)
            ->orderBy('name')
            ->get();

        return view('tenant.pages.variation-options.index', compact('tenant', 'variationOptions', 'variationTypes'));
    }

    public function create(string $slug, string $module)
    {
        //
    }

    public function store(Request $request, string $slug, string $module)
    {
        $validated = $request->validate([
            'variation_type_id' => 'required|exists:variation_types,id',
            'name' => 'required|string|max:255',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->status : true;
        $validated['order'] = VariationOption::max('order') + 1;

        VariationOption::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Opção de variação cadastrada com sucesso!'
        ]);
    }

    public function show(string $slug, string $module, string $code)
    {
        //
    }

    public function edit(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $variationOption = VariationOption::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $variationOption
        ]);
    }

    public function update(Request $request, string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $variationOption = VariationOption::findOrFail($id);

        $validated = $request->validate([
            'variation_type_id' => 'required|exists:variation_types,id',
            'name' => 'required|string|max:255',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->status : $variationOption->status;

        $variationOption->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Opção de variação atualizada com sucesso!'
        ]);
    }

    public function destroy(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $variationOption = VariationOption::findOrFail($id);
        $variationOption->delete();

        return response()->json([
            'success' => true,
            'message' => 'Opção de variação excluída com sucesso!'
        ]);
    }

    public function restore(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $variationOption = VariationOption::withTrashed()->findOrFail($id);
        $variationOption->restore();

        return response()->json([
            'success' => true,
            'message' => 'Opção de variação restaurada com sucesso!'
        ]);
    }

    public function reorder(Request $request, string $slug, string $module)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:variation_options,id',
        ]);

        foreach ($validated['order'] as $index => $id) {
            VariationOption::where('id', $id)->update(['order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ordem atualizada com sucesso!'
        ]);
    }
}
