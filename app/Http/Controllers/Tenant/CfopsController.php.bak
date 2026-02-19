<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Cfop;
use Illuminate\Http\Request;

class CfopsController extends Controller
{
    public function index(Request $request, string $slug, string $module)
    {
        $tenant = $request->tenant;
        $cfops = Cfop::orderBy('order')->orderBy('id')->get();

        return view('tenant.pages.cfops.index', compact('tenant', 'cfops'));
    }

    public function create(string $slug, string $module)
    {
        //
    }

    public function store(Request $request, string $slug, string $module)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:4',
            'description' => 'required|string|max:255',
            'type' => 'required|in:entry,exit',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->status : true;
        $validated['order'] = Cfop::max('order') + 1;

        Cfop::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'CFOP cadastrado com sucesso!'
        ]);
    }

    public function show(string $slug, string $module, string $code)
    {
        //
    }

    public function edit(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $cfop = Cfop::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $cfop
        ]);
    }

    public function update(Request $request, string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $cfop = Cfop::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|size:4',
            'description' => 'required|string|max:255',
            'type' => 'required|in:entry,exit',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->status : $cfop->status;

        $cfop->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'CFOP atualizado com sucesso!'
        ]);
    }

    public function destroy(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $cfop = Cfop::findOrFail($id);
        $cfop->delete();

        return response()->json([
            'success' => true,
            'message' => 'CFOP excluído com sucesso!'
        ]);
    }

    public function restore(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $cfop = Cfop::withTrashed()->findOrFail($id);
        $cfop->restore();

        return response()->json([
            'success' => true,
            'message' => 'CFOP restaurado com sucesso!'
        ]);
    }

    public function reorder(Request $request, string $slug, string $module)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:cfops,id',
        ]);

        foreach ($validated['order'] as $index => $id) {
            Cfop::where('id', $id)->update(['order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ordem atualizada com sucesso!'
        ]);
    }
}
