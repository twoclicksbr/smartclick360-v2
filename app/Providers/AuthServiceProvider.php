<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Configurar Passport para usar models customizados com conexão tenant
        Passport::useTokenModel(\App\Models\Passport\Token::class);
        Passport::useClientModel(\App\Models\Passport\Client::class);
        Passport::useAuthCodeModel(\App\Models\Passport\AuthCode::class);
        Passport::useRefreshTokenModel(\App\Models\Passport\RefreshToken::class);

        // Configurar tempo de expiração dos tokens
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }
}
