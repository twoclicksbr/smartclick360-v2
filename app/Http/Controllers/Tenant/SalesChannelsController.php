<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\SalesChannel;
use App\Models\Tenant\PriceList;
use Illuminate\Http\Request;

class SalesChannelsController extends Controller
{
    public function index(Request $request, string $slug, string $module)
    {
        $tenant = $request->tenant;
        $salesChannels = SalesChannel::with('priceList')
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $priceLists = PriceList::where('status', true)
            ->orderBy('name')
            ->get();

        return view('tenant.pages.sales-channels.index', compact('tenant', 'salesChannels', 'priceLists'));
    }

    public function create(string $slug, string $module)
    {
        //
    }

    public function store(Request $request, string $slug, string $module)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price_list_id' => 'nullable|exists:price_lists,id',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->status : true;
        $validated['order'] = SalesChannel::max('order') + 1;

        SalesChannel::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Canal de venda cadastrado com sucesso!'
        ]);
    }

    public function show(string $slug, string $module, string $code)
    {
        //
    }

    public function edit(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $salesChannel = SalesChannel::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $salesChannel
        ]);
    }

    public function update(Request $request, string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $salesChannel = SalesChannel::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price_list_id' => 'nullable|exists:price_lists,id',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->status : $salesChannel->status;

        $salesChannel->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Canal de venda atualizado com sucesso!'
        ]);
    }

    public function destroy(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $salesChannel = SalesChannel::findOrFail($id);
        $salesChannel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Canal de venda excluído com sucesso!'
        ]);
    }

    public function restore(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $salesChannel = SalesChannel::withTrashed()->findOrFail($id);
        $salesChannel->restore();

        return response()->json([
            'success' => true,
            'message' => 'Canal de venda restaurado com sucesso!'
        ]);
    }

    public function reorder(Request $request, string $slug, string $module)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:sales_channels,id',
        ]);

        foreach ($validated['order'] as $index => $id) {
            SalesChannel::where('id', $id)->update(['order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ordem atualizada com sucesso!'
        ]);
    }
}
