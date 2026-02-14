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
        return view('landlord.tenants.index');
    }

    /**
     * Exibe detalhes de um tenant específico
     */
    public function show(string $code)
    {
        return view('landlord.tenants.show');
    }
}
