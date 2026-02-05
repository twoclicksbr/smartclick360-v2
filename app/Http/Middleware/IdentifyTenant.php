<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Extrair o slug do subdomínio
        $host = $request->getHost();
        $baseDomain = config('app.tenant_domain', 'smartclick360.test');

        // Remove o domínio base para pegar apenas o slug
        $slug = str_replace('.' . $baseDomain, '', $host);

        // Se não houver slug (acesso direto ao domínio principal), retornar erro
        if ($slug === $host || empty($slug)) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'TENANT_NOT_FOUND',
                    'message' => 'Subdomínio não especificado',
                ],
            ], 404);
        }

        // Buscar o tenant no banco central
        $tenant = Tenant::where('slug', $slug)->first();

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'TENANT_NOT_FOUND',
                    'message' => 'Subdomínio não existe',
                ],
            ], 404);
        }

        // Verificar se o tenant está ativo
        if (!$tenant->isActive()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'TENANT_INACTIVE',
                    'message' => 'Tenant inativo ou suspenso',
                ],
            ], 403);
        }

        // Configurar a conexão do tenant dinamicamente
        Config::set('database.connections.tenant.database', $tenant->database_name);

        // Purge a conexão para forçar reconexão com o novo banco
        app('db')->purge('tenant');

        // Armazenar tenant na request para uso posterior
        $request->attributes->set('tenant', $tenant);

        return $next($request);
    }
}
