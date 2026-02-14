<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;

class TenantController extends Controller
{
    /**
     * Exibe a página de configurações do tenant
     */
    public function settings()
    {
        return view('tenant.pages.settings');
    }
}
