<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\DiscountTable;
use Illuminate\Http\Request;

class DiscountTablesController extends Controller
{
    public function index(Request $request, string $slug, string $module)
    {
        $tenant = $request->tenant;
        $discountTables = DiscountTable::withTrashed()->orderBy('order')->orderBy('id')->get();

        return view('tenant.pages.discount-tables.index', compact('tenant', 'discountTables'));
    }

    public function create(string $slug, string $module)
    {
        //
    }

    public function store(Request $request, string $slug, string $module)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'percentage' => 'required|numeric|min:0|max:100',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->status : true;
        $validated['order'] = DiscountTable::max('order') + 1;

        DiscountTable::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tabela de desconto cadastrada com sucesso!'
        ]);
    }

    public function show(string $slug, string $module, string $code)
    {
        //
    }

    public function edit(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $discountTable = DiscountTable::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $discountTable
        ]);
    }

    public function update(Request $request, string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $discountTable = DiscountTable::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'percentage' => 'required|numeric|min:0|max:100',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->status : $discountTable->status;

        $discountTable->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tabela de desconto atualizada com sucesso!'
        ]);
    }

    public function destroy(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $discountTable = DiscountTable::findOrFail($id);
        $discountTable->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tabela de desconto excluída com sucesso!'
        ]);
    }

    public function restore(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $discountTable = DiscountTable::withTrashed()->findOrFail($id);
        $discountTable->restore();

        return response()->json([
            'success' => true,
            'message' => 'Tabela de desconto restaurada com sucesso!'
        ]);
    }

    public function reorder(Request $request, string $slug, string $module)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:discount_tables,id',
        ]);

        foreach ($validated['order'] as $index => $id) {
            DiscountTable::where('id', $id)->update(['order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ordem atualizada com sucesso!'
        ]);
    }
}
