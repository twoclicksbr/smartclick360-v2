<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Ncm;
use Illuminate\Http\Request;

class NcmsController extends Controller
{
    public function index(Request $request, string $slug, string $module)
    {
        $tenant = $request->tenant;
        $ncms = Ncm::orderBy('order')->orderBy('id')->get();

        return view('tenant.pages.ncms.index', compact('tenant', 'ncms'));
    }

    public function create(string $slug, string $module)
    {
        //
    }

    public function store(Request $request, string $slug, string $module)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:8',
            'description' => 'required|string|max:255',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->status : true;
        $validated['order'] = Ncm::max('order') + 1;

        Ncm::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'NCM cadastrado com sucesso!'
        ]);
    }

    public function show(string $slug, string $module, string $code)
    {
        //
    }

    public function edit(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $ncm = Ncm::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $ncm
        ]);
    }

    public function update(Request $request, string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $ncm = Ncm::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|size:8',
            'description' => 'required|string|max:255',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->status : $ncm->status;

        $ncm->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'NCM atualizado com sucesso!'
        ]);
    }

    public function destroy(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $ncm = Ncm::findOrFail($id);
        $ncm->delete();

        return response()->json([
            'success' => true,
            'message' => 'NCM excluído com sucesso!'
        ]);
    }

    public function restore(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $ncm = Ncm::withTrashed()->findOrFail($id);
        $ncm->restore();

        return response()->json([
            'success' => true,
            'message' => 'NCM restaurado com sucesso!'
        ]);
    }

    public function reorder(Request $request, string $slug, string $module)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:ncms,id',
        ]);

        foreach ($validated['order'] as $index => $id) {
            Ncm::where('id', $id)->update(['order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ordem atualizada com sucesso!'
        ]);
    }
}
