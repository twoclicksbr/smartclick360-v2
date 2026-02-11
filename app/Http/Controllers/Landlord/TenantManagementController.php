<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Tenant;
use Illuminate\Http\Request;

class TenantManagementController extends Controller
{
    /**
     * Lista todos os tenants em formato de grid
     */
    public function index()
    {
        $tenants = Tenant::with(['subscriptions' => function($query) {
            $query->whereIn('status', ['active', 'trial'])
                  ->latest()
                  ->limit(1);
        }])
        ->withCount('subscriptions')
        ->orderBy('created_at', 'desc')
        ->get();

        return view('landlord.tenants.index', [
            'tenants' => $tenants,
        ]);
    }

    /**
     * Exibe detalhes de um tenant específico
     */
    public function show($id)
    {
        $tenant = Tenant::with(['subscriptions.plan'])
            ->findOrFail($id);

        return view('landlord.tenants.show', [
            'tenant' => $tenant,
        ]);
    }
}
