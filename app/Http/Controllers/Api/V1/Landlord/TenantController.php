<?php

namespace App\Http\Controllers\Api\V1\Landlord;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Landlord\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $tenants = Tenant::with(['subscriptions' => function($query) {
            $query->whereIn('status', ['active', 'trial'])
                  ->latest()
                  ->limit(1);
        }])
        ->withCount('subscriptions')
        ->orderBy('created_at', 'desc')
        ->get();

        // Adiciona encoded_id em cada tenant
        $tenants->each(function ($tenant) {
            $tenant->encoded_id = encodeId($tenant->id);
        });

        return $this->success([
            'tenants' => $tenants,
        ]);
    }

    public function show(Request $request, string $code): JsonResponse
    {
        // Decodifica o código para obter o ID
        $id = decodeId($code);

        $tenant = Tenant::with(['subscriptions.plan'])
            ->findOrFail($id);

        // Adiciona encoded_id
        $tenant->encoded_id = encodeId($tenant->id);

        return $this->success([
            'tenant' => $tenant,
        ]);
    }
}
