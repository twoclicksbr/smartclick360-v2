<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Origin;
use Illuminate\Http\Request;

class OriginsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, string $slug, string $module)
    {
        $origins = Origin::orderBy('order')->get();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $origins,
            ]);
        }

        return view('tenant.pages.origins.index', compact('origins'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $slug, string $module)
    {
        return view('tenant.pages.origins.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $slug, string $module)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:1',
            'description' => 'required|string|max:255',
        ], [
            'code.required' => 'O código é obrigatório',
            'code.size' => 'O código deve ter 1 caractere',
            'description.required' => 'A descrição é obrigatória',
        ]);

        $origin = Origin::create([
            'code' => $validated['code'],
            'description' => $validated['description'],
            'order' => Origin::max('order') + 1,
            'status' => $request->input('status', 1) == 1,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Origem criada com sucesso!',
                'data' => $origin,
            ]);
        }

        return redirect()->route('origins.index', ['slug' => $slug])
            ->with('success', 'Origem criada com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $origin = Origin::findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $origin,
            ]);
        }

        return view('tenant.pages.origins.edit', compact('origin'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $origin = Origin::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|size:1',
            'description' => 'required|string|max:255',
        ], [
            'code.required' => 'O código é obrigatório',
            'code.size' => 'O código deve ter 1 caractere',
            'description.required' => 'A descrição é obrigatória',
        ]);

        $origin->update([
            'code' => $validated['code'],
            'description' => $validated['description'],
            'status' => $request->input('status', 1) == 1,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Origem atualizada com sucesso!',
                'data' => $origin,
            ]);
        }

        return redirect()->route('origins.index', ['slug' => $slug])
            ->with('success', 'Origem atualizada com sucesso!');
    }

    /**
     * Soft delete the specified resource.
     */
    public function destroy(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $origin = Origin::findOrFail($id);

        $origin->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Origem excluída com sucesso!',
            ]);
        }

        return redirect()->route('origins.index', ['slug' => $slug])
            ->with('success', 'Origem excluída com sucesso!');
    }

    /**
     * Restore a soft deleted resource.
     */
    public function restore(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $origin = Origin::withTrashed()->findOrFail($id);

        $origin->restore();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Origem restaurada com sucesso!',
                'data' => $origin,
            ]);
        }

        return redirect()->route('origins.index', ['slug' => $slug])
            ->with('success', 'Origem restaurada com sucesso!');
    }

    /**
     * Reorder items via drag and drop
     */
    public function reorder(Request $request, string $slug, string $module)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:origins,id',
            'items.*.order' => 'required|integer',
        ]);

        foreach ($validated['items'] as $item) {
            Origin::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ordem atualizada com sucesso!',
            ]);
        }

        return redirect()->route('origins.index', ['slug' => $slug])
            ->with('success', 'Ordem atualizada com sucesso!');
    }
}
