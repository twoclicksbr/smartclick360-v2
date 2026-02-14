<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    /**
     * Sobrescreve findToken para buscar no banco correto.
     * Se não encontrar no landlord, tenta no tenant.
     */
    public static function findToken($token)
    {
        // Primeiro tenta no banco default (landlord)
        $model = parent::findToken($token);

        if ($model) {
            return $model;
        }

        // Se não encontrou, tenta no banco do tenant
        // O IdentifyTenant pode não ter rodado ainda, então precisamos
        // identificar o tenant pelo subdomínio da request
        $request = request();
        $host = $request->getHost();

        // Extrai o slug do subdomínio: {slug}.smartclick360-v2.test
        $parts = explode('.', $host);
        if (count($parts) < 3) {
            return null; // Não é subdomínio de tenant
        }

        $slug = $parts[0];
        $databaseName = 'sc360_' . $slug;

        // Verifica se o banco existe consultando o landlord
        $tenant = DB::connection('landlord')
            ->table('tenants')
            ->where('slug', $slug)
            ->where('status', 'active')
            ->first();

        if (!$tenant) {
            return null;
        }

        // Configura a conexão tenant
        Config::set('database.connections.tenant.database', $databaseName);
        Config::set('database.connections.tenant.search_path', 'production');
        DB::purge('tenant');
        DB::reconnect('tenant');

        // Busca o token no banco do tenant
        $instance = new static;
        $instance->setConnection('tenant');

        [$id, $plainToken] = explode('|', $token, 2);

        $tokenModel = $instance->getConnection()
            ->table($instance->getTable())
            ->where('id', $id)
            ->first();

        if ($tokenModel && hash_equals($tokenModel->token, hash('sha256', $plainToken))) {
            // Retorna um model completo com a conexão tenant
            $model = (new static)->setConnection('tenant')->newQuery()->find($tokenModel->id);
            return $model;
        }

        return null;
    }

    /**
     * Usa a conexão tenant se estiver configurada
     */
    public function getConnectionName()
    {
        // Se a conexão tenant já está configurada, usa ela
        if (Config::get('database.connections.tenant.database')) {
            return 'tenant';
        }

        return parent::getConnectionName();
    }
}
