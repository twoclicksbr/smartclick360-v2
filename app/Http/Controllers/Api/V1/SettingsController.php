<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Landlord\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Pega o stdClass do middleware
        $tenantData = $request->attributes->get('tenant');

        // Busca como model Eloquent para ter acesso aos relationships
        $tenant = Tenant::find($tenantData->id);

        if (!$tenant) {
            return $this->notFound('Tenant não encontrado');
        }

        // Busca assinatura ativa ou trial
        $subscription = $tenant->subscriptions()
            ->whereIn('status', ['active', 'trial'])
            ->with('plan')
            ->latest()
            ->first();

        // Busca todas as assinaturas (histórico)
        $subscriptionHistory = $tenant->subscriptions()
            ->with('plan')
            ->latest()
            ->get();

        return $this->success([
            'tenant' => $tenant,
            'user' => $user->load('person'),
            'subscription' => $subscription,
            'subscriptionHistory' => $subscriptionHistory,
        ]);
    }
}
