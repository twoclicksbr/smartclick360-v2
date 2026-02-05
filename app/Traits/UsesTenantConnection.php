<?php

namespace App\Traits;

use Illuminate\Support\Facades\Config;

trait UsesTenantConnection
{
    /**
     * Get the current connection name for the model.
     *
     * @return string|null
     */
    public function getConnectionName(): ?string
    {
        // Verifica se a conexão tenant está configurada
        if (Config::get('database.connections.tenant.database')) {
            return 'tenant';
        }

        // Fallback para a conexão padrão (evita erros em comandos artisan)
        return $this->connection ?? Config::get('database.default');
    }
}
