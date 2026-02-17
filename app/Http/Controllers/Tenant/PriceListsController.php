<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\PriceList;
use Illuminate\Http\Request;

class PriceListsController extends Controller
{
    public function index(Request $request, string $slug, string $module)
    {
        $tenant = $request->tenant;
        $priceLists = PriceList::orderBy('order')->orderBy('id')->get();

        return view('tenant.pages.price-lists.index', compact('tenant', 'priceLists'));
    }

    public function create(string $slug, string $module)
    {
        //
    }

    public function store(Request $request, string $slug, string $module)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:discount,addition',
            'percentage' => 'required|numeric|min:0|max:100',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->status : true;
        $validated['order'] = PriceList::max('order') + 1;

        PriceList::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tabela de preço cadastrada com sucesso!'
        ]);
    }

    public function show(string $slug, string $module, string $code)
    {
        //
    }

    public function edit(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $priceList = PriceList::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $priceList
        ]);
    }

    public function update(Request $request, string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $priceList = PriceList::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:discount,addition',
            'percentage' => 'required|numeric|min:0|max:100',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->status : $priceList->status;

        $priceList->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tabela de preço atualizada com sucesso!'
        ]);
    }

    public function destroy(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $priceList = PriceList::findOrFail($id);
        $priceList->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tabela de preço excluída com sucesso!'
        ]);
    }

    public function restore(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $priceList = PriceList::withTrashed()->findOrFail($id);
        $priceList->restore();

        return response()->json([
            'success' => true,
            'message' => 'Tabela de preço restaurada com sucesso!'
        ]);
    }

    public function reorder(Request $request, string $slug, string $module)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:price_lists,id',
        ]);

        foreach ($validated['order'] as $index => $id) {
            PriceList::where('id', $id)->update(['order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ordem atualizada com sucesso!'
        ]);
    }
}
