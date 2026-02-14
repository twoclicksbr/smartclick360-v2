<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — v1
|--------------------------------------------------------------------------
|
| Todas as rotas da API são prefixadas com /api/v1
| Autenticação via Laravel Sanctum (Bearer Token)
|
*/

Route::prefix('v1')->group(function () {

    // ─── Landlord Auth (público) ────────────────────────
    Route::post('/auth/landlord/login', [\App\Http\Controllers\Api\V1\Auth\LandlordAuthController::class, 'login']);

    // ─── Landlord protegido ─────────────────────────────
    Route::middleware(['auth:sanctum'])->prefix('landlord')->group(function () {
        Route::post('/auth/logout', [\App\Http\Controllers\Api\V1\Auth\LandlordAuthController::class, 'logout']);
        Route::get('/auth/me', [\App\Http\Controllers\Api\V1\Auth\LandlordAuthController::class, 'me']);
        Route::get('/dashboard', [\App\Http\Controllers\Api\V1\Landlord\DashboardController::class, 'index']);
        Route::get('/tenants', [\App\Http\Controllers\Api\V1\Landlord\TenantController::class, 'index']);
        Route::get('/tenants/{code}', [\App\Http\Controllers\Api\V1\Landlord\TenantController::class, 'show']);
    });

    // ─── Tenant (todas as rotas com identify.tenant) ───
    Route::middleware(['identify.tenant'])->group(function () {

        // Auth tenant (público — sem auth:sanctum)
        Route::post('/auth/tenant/login', [\App\Http\Controllers\Api\V1\Auth\TenantAuthController::class, 'login']);

        // Protegido (com auth:sanctum)
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::post('/auth/tenant/logout', [\App\Http\Controllers\Api\V1\Auth\TenantAuthController::class, 'logout']);
            Route::get('/auth/tenant/me', [\App\Http\Controllers\Api\V1\Auth\TenantAuthController::class, 'me']);

            Route::get('/dashboard', [\App\Http\Controllers\Api\V1\DashboardController::class, 'index']);
            Route::get('/settings', [\App\Http\Controllers\Api\V1\SettingsController::class, 'index']);

            // Módulos genéricos
            Route::prefix('{module}')->group(function () {
                Route::get('/', [\App\Http\Controllers\Api\V1\ModuleController::class, 'index']);
                Route::post('/', [\App\Http\Controllers\Api\V1\ModuleController::class, 'store']);
                Route::post('/reorder', [\App\Http\Controllers\Api\V1\ModuleController::class, 'reorder']);
                Route::get('/{code}', [\App\Http\Controllers\Api\V1\ModuleController::class, 'show']);
                Route::put('/{code}', [\App\Http\Controllers\Api\V1\ModuleController::class, 'update']);
                Route::delete('/{code}', [\App\Http\Controllers\Api\V1\ModuleController::class, 'destroy']);
                Route::patch('/{code}/restore', [\App\Http\Controllers\Api\V1\ModuleController::class, 'restore']);

                // Submódulos
                Route::prefix('{code}/{submodule}')->group(function () {
                    Route::get('/', [\App\Http\Controllers\Api\V1\SubmoduleController::class, 'index']);
                    Route::post('/', [\App\Http\Controllers\Api\V1\SubmoduleController::class, 'store']);
                    Route::post('/reorder', [\App\Http\Controllers\Api\V1\SubmoduleController::class, 'reorder']);
                    Route::get('/{s_code}', [\App\Http\Controllers\Api\V1\SubmoduleController::class, 'show']);
                    Route::put('/{s_code}', [\App\Http\Controllers\Api\V1\SubmoduleController::class, 'update']);
                    Route::delete('/{s_code}', [\App\Http\Controllers\Api\V1\SubmoduleController::class, 'destroy']);
                    Route::patch('/{s_code}/restore', [\App\Http\Controllers\Api\V1\SubmoduleController::class, 'restore']);
                });
            });
        });
    });
});
