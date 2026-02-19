<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\TypeProduct;
use Illuminate\Http\Request;

class TypeProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, string $slug, string $module)
    {
        $typeProducts = TypeProduct::orderBy('order')->get();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $typeProducts,
            ]);
        }

        return view('tenant.pages.type-products.index', compact('typeProducts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $slug, string $module)
    {
        return view('tenant.pages.type-products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $slug, string $module)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:product,service',
        ], [
            'name.required' => 'O nome é obrigatório',
            'type.required' => 'O tipo é obrigatório',
            'type.in' => 'O tipo deve ser produto ou serviço',
        ]);

        $typeProduct = TypeProduct::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'order' => TypeProduct::max('order') + 1,
            'status' => $request->input('status', 1) == 1,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tipo de produto criado com sucesso!',
                'data' => $typeProduct,
            ]);
        }

        return redirect()->route('type-products.index', ['slug' => $slug])
            ->with('success', 'Tipo de produto criado com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $typeProduct = TypeProduct::findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $typeProduct,
            ]);
        }

        return view('tenant.pages.type-products.edit', compact('typeProduct'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $typeProduct = TypeProduct::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:product,service',
        ], [
            'name.required' => 'O nome é obrigatório',
            'type.required' => 'O tipo é obrigatório',
            'type.in' => 'O tipo deve ser produto ou serviço',
        ]);

        $typeProduct->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'status' => $request->input('status', 1) == 1,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tipo de produto atualizado com sucesso!',
                'data' => $typeProduct,
            ]);
        }

        return redirect()->route('type-products.index', ['slug' => $slug])
            ->with('success', 'Tipo de produto atualizado com sucesso!');
    }

    /**
     * Soft delete the specified resource.
     */
    public function destroy(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $typeProduct = TypeProduct::findOrFail($id);

        $typeProduct->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tipo de produto excluído com sucesso!',
            ]);
        }

        return redirect()->route('type-products.index', ['slug' => $slug])
            ->with('success', 'Tipo de produto excluído com sucesso!');
    }

    /**
     * Restore a soft deleted resource.
     */
    public function restore(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $typeProduct = TypeProduct::withTrashed()->findOrFail($id);

        $typeProduct->restore();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tipo de produto restaurado com sucesso!',
                'data' => $typeProduct,
            ]);
        }

        return redirect()->route('type-products.index', ['slug' => $slug])
            ->with('success', 'Tipo de produto restaurado com sucesso!');
    }

    /**
     * Reorder items via drag and drop
     */
    public function reorder(Request $request, string $slug, string $module)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:type_products,id',
            'items.*.order' => 'required|integer',
        ]);

        foreach ($validated['items'] as $item) {
            TypeProduct::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ordem atualizada com sucesso!',
            ]);
        }

        return redirect()->route('type-products.index', ['slug' => $slug])
            ->with('success', 'Ordem atualizada com sucesso!');
    }
}
