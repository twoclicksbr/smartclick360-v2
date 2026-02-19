<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Warehouse;
use Illuminate\Http\Request;

class WarehousesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, string $slug, string $module)
    {
        $warehouses = Warehouse::orderBy('order')->get();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $warehouses,
            ]);
        }

        return view('tenant.pages.warehouses.index', compact('warehouses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $slug, string $module)
    {
        return view('tenant.pages.warehouses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $slug, string $module)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'O nome é obrigatório',
        ]);

        $warehouse = Warehouse::create([
            'name' => $validated['name'],
            'order' => Warehouse::max('order') + 1,
            'status' => $request->input('status', 1) == 1,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Local de estoque criado com sucesso!',
                'data' => $warehouse,
            ]);
        }

        return redirect()->route('warehouses.index', ['slug' => $slug])
            ->with('success', 'Local de estoque criado com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $warehouse = Warehouse::findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $warehouse,
            ]);
        }

        return view('tenant.pages.warehouses.edit', compact('warehouse'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $warehouse = Warehouse::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'O nome é obrigatório',
        ]);

        $warehouse->update([
            'name' => $validated['name'],
            'status' => $request->input('status', 1) == 1,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Local de estoque atualizado com sucesso!',
                'data' => $warehouse,
            ]);
        }

        return redirect()->route('warehouses.index', ['slug' => $slug])
            ->with('success', 'Local de estoque atualizado com sucesso!');
    }

    /**
     * Soft delete the specified resource.
     */
    public function destroy(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $warehouse = Warehouse::findOrFail($id);

        $warehouse->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Local de estoque excluído com sucesso!',
            ]);
        }

        return redirect()->route('warehouses.index', ['slug' => $slug])
            ->with('success', 'Local de estoque excluído com sucesso!');
    }

    /**
     * Restore a soft deleted resource.
     */
    public function restore(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $warehouse = Warehouse::withTrashed()->findOrFail($id);

        $warehouse->restore();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Local de estoque restaurado com sucesso!',
                'data' => $warehouse,
            ]);
        }

        return redirect()->route('warehouses.index', ['slug' => $slug])
            ->with('success', 'Local de estoque restaurado com sucesso!');
    }

    /**
     * Reorder items via drag and drop
     */
    public function reorder(Request $request, string $slug, string $module)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:warehouses,id',
            'items.*.order' => 'required|integer',
        ]);

        foreach ($validated['items'] as $item) {
            Warehouse::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ordem atualizada com sucesso!',
            ]);
        }

        return redirect()->route('warehouses.index', ['slug' => $slug])
            ->with('success', 'Ordem atualizada com sucesso!');
    }
}
