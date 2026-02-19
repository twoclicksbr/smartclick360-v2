<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Group;
use Illuminate\Http\Request;

class GroupsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, string $slug, string $module)
    {
        $groups = Group::orderBy('order')->get();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $groups,
            ]);
        }

        return view('tenant.pages.groups.index', compact('groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $slug, string $module)
    {
        return view('tenant.pages.groups.create');
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

        $group = Group::create([
            'name' => $validated['name'],
            'order' => Group::max('order') + 1,
            'status' => $request->input('status', 1) == 1,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Grupo criado com sucesso!',
                'data' => $group,
            ]);
        }

        return redirect()->route('groups.index', ['slug' => $slug])
            ->with('success', 'Grupo criado com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $group = Group::findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $group,
            ]);
        }

        return view('tenant.pages.groups.edit', compact('group'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $group = Group::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'O nome é obrigatório',
        ]);

        $group->update([
            'name' => $validated['name'],
            'status' => $request->input('status', 1) == 1,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Grupo atualizado com sucesso!',
                'data' => $group,
            ]);
        }

        return redirect()->route('groups.index', ['slug' => $slug])
            ->with('success', 'Grupo atualizado com sucesso!');
    }

    /**
     * Soft delete the specified resource.
     */
    public function destroy(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $group = Group::findOrFail($id);

        $group->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Grupo excluído com sucesso!',
            ]);
        }

        return redirect()->route('groups.index', ['slug' => $slug])
            ->with('success', 'Grupo excluído com sucesso!');
    }

    /**
     * Restore a soft deleted resource.
     */
    public function restore(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $group = Group::withTrashed()->findOrFail($id);

        $group->restore();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Grupo restaurado com sucesso!',
                'data' => $group,
            ]);
        }

        return redirect()->route('groups.index', ['slug' => $slug])
            ->with('success', 'Grupo restaurado com sucesso!');
    }

    /**
     * Reorder items via drag and drop
     */
    public function reorder(Request $request, string $slug, string $module)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:groups,id',
            'items.*.order' => 'required|integer',
        ]);

        foreach ($validated['items'] as $item) {
            Group::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ordem atualizada com sucesso!',
            ]);
        }

        return redirect()->route('groups.index', ['slug' => $slug])
            ->with('success', 'Ordem atualizada com sucesso!');
    }
}
