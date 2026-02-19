<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Family;
use Illuminate\Http\Request;

class FamiliesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, string $slug, string $module)
    {
        $families = Family::orderBy('order')->get();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $families,
            ]);
        }

        return view('tenant.pages.families.index', compact('families'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $slug, string $module)
    {
        return view('tenant.pages.families.create');
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

        $family = Family::create([
            'name' => $validated['name'],
            'order' => Family::max('order') + 1,
            'status' => $request->input('status', 1) == 1,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Família criada com sucesso!',
                'data' => $family,
            ]);
        }

        return redirect()->route('families.index', ['slug' => $slug])
            ->with('success', 'Família criada com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $family = Family::findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $family,
            ]);
        }

        return view('tenant.pages.families.edit', compact('family'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $family = Family::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'O nome é obrigatório',
        ]);

        $family->update([
            'name' => $validated['name'],
            'status' => $request->input('status', 1) == 1,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Família atualizada com sucesso!',
                'data' => $family,
            ]);
        }

        return redirect()->route('families.index', ['slug' => $slug])
            ->with('success', 'Família atualizada com sucesso!');
    }

    /**
     * Soft delete the specified resource.
     */
    public function destroy(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $family = Family::findOrFail($id);

        $family->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Família excluída com sucesso!',
            ]);
        }

        return redirect()->route('families.index', ['slug' => $slug])
            ->with('success', 'Família excluída com sucesso!');
    }

    /**
     * Restore a soft deleted resource.
     */
    public function restore(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $family = Family::withTrashed()->findOrFail($id);

        $family->restore();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Família restaurada com sucesso!',
                'data' => $family,
            ]);
        }

        return redirect()->route('families.index', ['slug' => $slug])
            ->with('success', 'Família restaurada com sucesso!');
    }

    /**
     * Reorder items via drag and drop
     */
    public function reorder(Request $request, string $slug, string $module)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:families,id',
            'items.*.order' => 'required|integer',
        ]);

        foreach ($validated['items'] as $item) {
            Family::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ordem atualizada com sucesso!',
            ]);
        }

        return redirect()->route('families.index', ['slug' => $slug])
            ->with('success', 'Ordem atualizada com sucesso!');
    }
}
