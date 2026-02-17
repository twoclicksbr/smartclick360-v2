<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Brand;
use Illuminate\Http\Request;

class BrandsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, string $slug, string $module)
    {
        $brands = Brand::orderBy('order')->get();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $brands,
            ]);
        }

        return view('tenant.pages.brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $slug, string $module)
    {
        return view('tenant.pages.brands.create');
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

        $brand = Brand::create([
            'name' => $validated['name'],
            'order' => Brand::max('order') + 1,
            'status' => $request->input('status', 1) == 1,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Marca criada com sucesso!',
                'data' => $brand,
            ]);
        }

        return redirect()->route('brands.index', ['slug' => $slug])
            ->with('success', 'Marca criada com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $brand = Brand::findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $brand,
            ]);
        }

        return view('tenant.pages.brands.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $brand = Brand::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'O nome é obrigatório',
        ]);

        $brand->update([
            'name' => $validated['name'],
            'status' => $request->input('status', 1) == 1,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Marca atualizada com sucesso!',
                'data' => $brand,
            ]);
        }

        return redirect()->route('brands.index', ['slug' => $slug])
            ->with('success', 'Marca atualizada com sucesso!');
    }

    /**
     * Soft delete the specified resource.
     */
    public function destroy(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $brand = Brand::findOrFail($id);

        $brand->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Marca excluída com sucesso!',
            ]);
        }

        return redirect()->route('brands.index', ['slug' => $slug])
            ->with('success', 'Marca excluída com sucesso!');
    }

    /**
     * Restore a soft deleted resource.
     */
    public function restore(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $brand = Brand::withTrashed()->findOrFail($id);

        $brand->restore();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Marca restaurada com sucesso!',
                'data' => $brand,
            ]);
        }

        return redirect()->route('brands.index', ['slug' => $slug])
            ->with('success', 'Marca restaurada com sucesso!');
    }

    /**
     * Reorder items via drag and drop
     */
    public function reorder(Request $request, string $slug, string $module)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:brands,id',
            'items.*.order' => 'required|integer',
        ]);

        foreach ($validated['items'] as $item) {
            Brand::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ordem atualizada com sucesso!',
            ]);
        }

        return redirect()->route('brands.index', ['slug' => $slug])
            ->with('success', 'Ordem atualizada com sucesso!');
    }
}
