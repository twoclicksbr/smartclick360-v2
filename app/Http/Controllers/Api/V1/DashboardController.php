<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Tenant\Person;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $person = $user->person;
        $tenant = $request->attributes->get('tenant');

        // Estatísticas básicas do tenant
        $stats = [
            'total_people' => Person::count(),
            'active_people' => Person::where('status', true)->count(),
        ];

        return $this->success([
            'user' => $user->load('person'),
            'tenant' => $tenant,
            'stats' => $stats,
        ]);
    }
}
