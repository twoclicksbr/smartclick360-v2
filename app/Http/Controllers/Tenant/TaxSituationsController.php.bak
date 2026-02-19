<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\TaxSituation;
use Illuminate\Http\Request;

class TaxSituationsController extends Controller
{
    public function index(Request $request, string $slug, string $module)
    {
        $tenant = $request->tenant;
        $taxSituations = TaxSituation::orderBy('order')->orderBy('id')->get();

        return view('tenant.pages.tax-situations.index', compact('tenant', 'taxSituations'));
    }

    public function create(string $slug, string $module)
    {
        //
    }

    public function store(Request $request, string $slug, string $module)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:3',
            'description' => 'required|string|max:255',
            'regime' => 'required|in:normal,simples',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->status : true;
        $validated['order'] = TaxSituation::max('order') + 1;

        TaxSituation::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Situação tributária cadastrada com sucesso!'
        ]);
    }

    public function show(string $slug, string $module, string $code)
    {
        //
    }

    public function edit(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $taxSituation = TaxSituation::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $taxSituation
        ]);
    }

    public function update(Request $request, string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $taxSituation = TaxSituation::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:3',
            'description' => 'required|string|max:255',
            'regime' => 'required|in:normal,simples',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->status : $taxSituation->status;

        $taxSituation->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Situação tributária atualizada com sucesso!'
        ]);
    }

    public function destroy(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $taxSituation = TaxSituation::findOrFail($id);
        $taxSituation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Situação tributária excluída com sucesso!'
        ]);
    }

    public function restore(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $taxSituation = TaxSituation::withTrashed()->findOrFail($id);
        $taxSituation->restore();

        return response()->json([
            'success' => true,
            'message' => 'Situação tributária restaurada com sucesso!'
        ]);
    }

    public function reorder(Request $request, string $slug, string $module)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:tax_situations,id',
        ]);

        foreach ($validated['order'] as $index => $id) {
            TaxSituation::where('id', $id)->update(['order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ordem atualizada com sucesso!'
        ]);
    }
}
