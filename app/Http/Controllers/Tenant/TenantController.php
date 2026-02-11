<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Tenant;
use Illuminate\Support\Facades\Auth;

class TenantController extends Controller
{
    /**
     * Exibe a página de configurações do tenant
     */
    public function settings()
    {
        $user = Auth::guard('tenant')->user();
        $tenantSlug = request()->route('slug');
        $tenant = Tenant::where('slug', $tenantSlug)->first();

        if (!$tenant) {
            abort(404, 'Tenant não encontrado');
        }

        // Busca assinatura ativa ou trial
        $subscription = $tenant->subscriptions()
            ->whereIn('status', ['active', 'trial'])
            ->latest()
            ->first();

        // Busca todas as assinaturas (histórico)
        $subscriptionHistory = $tenant->subscriptions()
            ->with('plan')
            ->latest()
            ->get();

        return view('tenant.settings', [
            'tenant' => $tenant,
            'user' => $user,
            'subscription' => $subscription,
            'subscriptionHistory' => $subscriptionHistory,
        ]);
    }
}
