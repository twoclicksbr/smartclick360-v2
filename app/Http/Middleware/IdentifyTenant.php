<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        // Extrair slug do subdomínio
        // Ex: alex.smartclick360-v2.test → slug = "alex"
        // Ex: smartclick360-v2.test → slug = null
        $parts = explode('.', $host);

        // Se não tem subdomínio (domínio principal), abort
        // smartclick360-v2.test = 2 partes
        // alex.smartclick360-v2.test = 3 partes
        if (count($parts) < 3) {
            abort(404, 'Tenant não identificado.');
        }

        $slug = $parts[0];

        // Buscar tenant no banco central (landlord)
        $tenant = DB::connection('landlord')
            ->table('tenants')
            ->where('slug', $slug)
            ->whereNull('deleted_at')
            ->first();

        if (!$tenant) {
            abort(404, 'Empresa não encontrada.');
        }

        if ($tenant->status !== 'active') {
            abort(403, 'Esta conta está suspensa. Entre em contato com o suporte.');
        }

        // Configurar conexão tenant em runtime
        Config::set('database.connections.tenant.database', $tenant->database_name);
        Config::set('database.connections.tenant.schema', env('TENANT_SCHEMA', 'production'));

        // Purgar conexão anterior (para reconectar com novo banco)
        DB::purge('tenant');
        DB::reconnect('tenant');

        // Setar search_path para production
        DB::connection('tenant')->statement("SET search_path TO " . env('TENANT_SCHEMA', 'production'));

        // Armazenar tenant no request e session
        $request->attributes->set('tenant', $tenant);
        $request->attributes->set('tenant_id', $tenant->id);

        session([
            'tenant_id'   => $tenant->id,
            'tenant_slug' => $tenant->slug,
            'tenant_name' => $tenant->name,
            'tenant_db'   => $tenant->database_name,
        ]);

        // Compartilhar tenant com todas as views
        View::share('tenant', $tenant);

        return $next($request);
    }
}
