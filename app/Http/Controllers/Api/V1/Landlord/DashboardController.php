<?php

namespace App\Http\Controllers\Api\V1\Landlord;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Landlord\Tenant;
use App\Models\Landlord\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('status', 'active')->count();
        $trialSubscriptions = Subscription::where('status', 'trial')->count();
        $activeSubscriptions = Subscription::where('status', 'active')->count();

        $recentTenants = Tenant::with(['subscriptions.plan'])
            ->latest()
            ->take(5)
            ->get();

        return $this->success([
            'stats' => [
                'total_tenants' => $totalTenants,
                'active_tenants' => $activeTenants,
                'trial_subscriptions' => $trialSubscriptions,
                'active_subscriptions' => $activeSubscriptions,
            ],
            'recent_tenants' => $recentTenants,
        ]);
    }
}
