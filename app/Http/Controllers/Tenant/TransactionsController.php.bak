<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Transaction;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, string $slug, string $module)
    {
        $tenant = $request->tenant;
        $transactions = Transaction::withTrashed()->orderBy('order')->orderBy('id')->get();

        return view('tenant.pages.transactions.index', compact('tenant', 'transactions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $slug, string $module)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $slug, string $module)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:sale,purchase,return_sale,return_purchase,adjustment_in,adjustment_out,transfer,bonus,quote,consignment',
            'stock_movement' => 'required|string|in:in,out,none',
            'financial_impact' => 'required|string|in:receivable,payable,none',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->status : true;
        $validated['order'] = Transaction::max('order') + 1;

        Transaction::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Transação cadastrada com sucesso!'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug, string $module, string $code)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $transaction = Transaction::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $transaction
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $transaction = Transaction::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:sale,purchase,return_sale,return_purchase,adjustment_in,adjustment_out,transfer,bonus,quote,consignment',
            'stock_movement' => 'required|string|in:in,out,none',
            'financial_impact' => 'required|string|in:receivable,payable,none',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $request->has('status') ? (bool) $request->status : $transaction->status;

        $transaction->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Transação atualizada com sucesso!'
        ]);
    }

    /**
     * Soft delete the specified resource.
     */
    public function destroy(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transação excluída com sucesso!'
        ]);
    }

    /**
     * Restore a soft deleted resource.
     */
    public function restore(string $slug, string $module, string $code)
    {
        $id = decodeId($code);
        $transaction = Transaction::withTrashed()->findOrFail($id);
        $transaction->restore();

        return response()->json([
            'success' => true,
            'message' => 'Transação restaurada com sucesso!'
        ]);
    }

    /**
     * Reorder items via drag and drop
     */
    public function reorder(Request $request, string $slug, string $module)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:transactions,id',
        ]);

        foreach ($validated['order'] as $index => $id) {
            Transaction::where('id', $id)->update(['order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ordem atualizada com sucesso!'
        ]);
    }
}
