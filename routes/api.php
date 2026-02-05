<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PersonController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Todas as rotas da API são protegidas pelo middleware 'tenant' (global)
| que identifica o tenant pelo subdomínio e configura a conexão do banco.
|
| Formato: {tenant}.smartclick360.test/api/v1/...
|
*/

// Grupo de rotas da API v1
Route::prefix(env('API_VERSION', 'v1'))->group(function () {

    // Rotas públicas (sem autenticação)
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
    });

    // Rotas protegidas (requerem autenticação) - Todas sob /admin
    Route::prefix('admin')->middleware(['auth:api'])->group(function () {

        // Auth
        Route::prefix('auth')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
        });

        // Persons (CRUD + Restore)
        Route::apiResource('persons', PersonController::class);
        Route::patch('persons/{id}/restore', [PersonController::class, 'restore']);

    });
});
