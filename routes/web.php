<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LandlordLoginController;
use App\Http\Controllers\Tenant\TenantController;
use App\Http\Controllers\Landlord\TenantManagementController;

/*
|--------------------------------------------------------------------------
| Rotas do Domínio Principal (smartclick360-v2.test)
|--------------------------------------------------------------------------
|
| Páginas públicas: home, about, pricing, register
| Estas rotas NÃO passam pelo middleware identify.tenant
|
*/

Route::domain('smartclick360-v2.test')->group(function () {

    // Landing pages
    Route::get('/', [PageController::class, 'home'])->name('home');
    Route::get('/about', [PageController::class, 'about'])->name('about');
    Route::get('/pricing', [PageController::class, 'pricing'])->name('pricing');

    // Registro (criação de novo tenant)
    Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

    // Validações AJAX do registro
    Route::post('/check-slug', [RegisterController::class, 'checkSlug'])->name('check.slug');
    Route::post('/check-email', [RegisterController::class, 'checkEmail'])->name('check.email');
    Route::post('/check-document', [RegisterController::class, 'checkDocument'])->name('check.document');

    // Login admin (landlord)
    Route::get('/login', [LandlordLoginController::class, 'showForm'])->name('landlord.login');
    Route::post('/login', [LandlordLoginController::class, 'authenticate'])->name('landlord.authenticate');
    Route::post('/logout', [LandlordLoginController::class, 'logout'])->name('landlord.logout');

    // Dashboard admin (protegido por auth)
    Route::middleware('auth:web')->group(function () {
        Route::get('/dashboard', function () {
            $user = Auth::guard('web')->user();
            return view('landlord.dashboard', [
                'user' => $user,
            ]);
        })->name('landlord.dashboard');

        // Gestão de Tenants
        Route::prefix('tenants')->name('landlord.tenants.')->group(function () {
            Route::get('/', [TenantManagementController::class, 'index'])->name('index');
            Route::get('/{id}', [TenantManagementController::class, 'show'])->name('show');
        });
    });
});

/*
|--------------------------------------------------------------------------
| Rotas de Tenant ({slug}.smartclick360-v2.test)
|--------------------------------------------------------------------------
|
| Todas as rotas aqui passam pelo middleware identify.tenant
| O middleware identifica o tenant pelo subdomínio e configura a conexão
|
*/

Route::domain('{slug}.smartclick360-v2.test')->middleware('identify.tenant')->group(function () {

    // Raiz do subdomínio → redireciona para login
    Route::get('/', function () {
        return redirect()->route('tenant.login');
    });

    // Login
    Route::get('/login', [LoginController::class, 'showForm'])->name('tenant.login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('tenant.authenticate');

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('tenant.logout');

    // Dashboard e área protegida do tenant
    Route::middleware('auth:tenant')->group(function () {
        // Dashboard
        Route::get('/dashboard/main', function () {
            $tenant = request()->attributes->get('tenant');
            $user = Auth::guard('tenant')->user();
            return view('tenant.pages.dashboard.main', [
                'tenant' => $tenant,
                'user'   => $user,
            ]);
        })->name('tenant.dashboard.main');

        // Configurações do Tenant
        Route::get('/settings', [TenantController::class, 'settings'])->name('tenant.settings');

        // Rotas específicas do módulo People
        Route::get('people/{id}/files', [\App\Http\Controllers\Tenant\PeopleController::class, 'showFiles'])->name('people.files');

        // Submódulos - rotas genéricas (DEVEM VIR ANTES das rotas de módulos)
        Route::post('{module}/{m_id}/{submodule}/reorder', [\App\Http\Controllers\Tenant\SubmoduleController::class, 'reorder'])->name('submodule.reorder');
        Route::get('{module}/{m_id}/{submodule}', [\App\Http\Controllers\Tenant\SubmoduleController::class, 'index'])->name('submodule.index');
        Route::get('{module}/{m_id}/{submodule}/create', [\App\Http\Controllers\Tenant\SubmoduleController::class, 'create'])->name('submodule.create');
        Route::post('{module}/{m_id}/{submodule}', [\App\Http\Controllers\Tenant\SubmoduleController::class, 'store'])->name('submodule.store');
        Route::get('{module}/{m_id}/{submodule}/{s_id}', [\App\Http\Controllers\Tenant\SubmoduleController::class, 'show'])->name('submodule.show');
        Route::get('{module}/{m_id}/{submodule}/{s_id}/edit', [\App\Http\Controllers\Tenant\SubmoduleController::class, 'edit'])->name('submodule.edit');
        Route::put('{module}/{m_id}/{submodule}/{s_id}', [\App\Http\Controllers\Tenant\SubmoduleController::class, 'update'])->name('submodule.update');
        Route::patch('{module}/{m_id}/{submodule}/{s_id}', [\App\Http\Controllers\Tenant\SubmoduleController::class, 'update'])->name('submodule.patch');
        Route::delete('{module}/{m_id}/{submodule}/{s_id}', [\App\Http\Controllers\Tenant\SubmoduleController::class, 'destroy'])->name('submodule.destroy');
        Route::patch('{module}/{m_id}/{submodule}/{s_id}/restore', [\App\Http\Controllers\Tenant\SubmoduleController::class, 'restore'])->name('submodule.restore');

        // Módulos - rotas genéricas (DEVEM VIR DEPOIS das rotas de submódulos)
        Route::get('{module}', [\App\Http\Controllers\Tenant\ModuleController::class, 'index'])->name('module.index');
        Route::get('{module}/create', [\App\Http\Controllers\Tenant\ModuleController::class, 'create'])->name('module.create');
        Route::post('{module}', [\App\Http\Controllers\Tenant\ModuleController::class, 'store'])->name('module.store');
        Route::get('{module}/{id}', [\App\Http\Controllers\Tenant\ModuleController::class, 'show'])->name('module.show');
        Route::get('{module}/{id}/edit', [\App\Http\Controllers\Tenant\ModuleController::class, 'edit'])->name('module.edit');
        Route::put('{module}/{id}', [\App\Http\Controllers\Tenant\ModuleController::class, 'update'])->name('module.update');
        Route::patch('{module}/{id}', [\App\Http\Controllers\Tenant\ModuleController::class, 'update'])->name('module.patch');
        Route::delete('{module}/{id}', [\App\Http\Controllers\Tenant\ModuleController::class, 'destroy'])->name('module.destroy');
        Route::post('{module}/reorder', [\App\Http\Controllers\Tenant\ModuleController::class, 'reorder'])->name('module.reorder');
        Route::patch('{module}/{id}/restore', [\App\Http\Controllers\Tenant\ModuleController::class, 'restore'])->name('module.restore');
    });
});
