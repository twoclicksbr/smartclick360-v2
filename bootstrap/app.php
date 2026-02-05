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
        // Middleware alias para identificação de tenant
        $middleware->alias([
            'tenant' => \App\Http\Middleware\IdentifyTenant::class,
        ]);

        // Adicionar tenant middleware globalmente para rotas da API
        // Isso garante que rode ANTES da autenticação do Passport
        $middleware->api(prepend: [
            \App\Http\Middleware\IdentifyTenant::class,
        ]);

        // Configurar comportamento de autenticação para APIs (não redirecionar)
        $middleware->redirectGuestsTo(fn () => null);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Retornar JSON para erros de autenticação em APIs
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 401,
                    'endpoint' => $request->method() . ' ' . $request->path(),
                    'success' => false,
                    'message' => 'Unauthenticated. Token is missing or invalid.',
                    'error' => 'authentication_failed'
                ], 401);
            }
        });

        // Retornar JSON para erros 404 em APIs
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 404,
                    'endpoint' => $request->method() . ' ' . $request->path(),
                    'success' => false,
                    'message' => 'The requested resource was not found.',
                    'error' => 'not_found'
                ], 404);
            }
        });

        // Retornar JSON para erros 405 (Method Not Allowed) em APIs
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 405,
                    'endpoint' => $request->method() . ' ' . $request->path(),
                    'success' => false,
                    'message' => 'The HTTP method is not allowed for this endpoint.',
                    'error' => 'method_not_allowed',
                    'allowed_methods' => $e->getHeaders()['Allow'] ?? 'unknown'
                ], 405);
            }
        });
    })->create();
