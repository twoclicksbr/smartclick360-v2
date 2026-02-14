<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'identify.tenant' => \App\Http\Middleware\IdentifyTenant::class,
        ]);

        // Adiciona middleware Sanctum SPA para autenticação via cookie/session
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        // Garante que IdentifyTenant rode antes do EnsureFrontendRequestsAreStateful e Authenticate
        $middleware->priority([
            \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\IdentifyTenant::class, // ANTES do Sanctum e Authenticate
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Auth\Middleware\Authenticate::class,
            \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            \Illuminate\Auth\Middleware\Authorize::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class,
            \Illuminate\Routing\Middleware\ThrottleRequestsWithRedis::class,
            \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
        ]);

        // Redireciona usuários não autenticados para /login
        $middleware->redirectGuestsTo(fn () => url('/login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, $request) {
            $response = \App\Exceptions\ApiExceptionHandler::handle($e, $request);
            if ($response) {
                return $response;
            }
        });
    })->create();
