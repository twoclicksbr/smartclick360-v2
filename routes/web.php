<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LandlordLoginController;
use App\Http\Controllers\Tenant\TenantController;
use App\Http\Controllers\Landlord\TenantManagementController;

// TESTE

/*
|--------------------------------------------------------------------------
| Rotas do Domínio Principal (smartclick360-v2.test)
|--------------------------------------------------------------------------
|
| Páginas públicas: home, about, pricing, register
| Estas rotas NÃO passam pelo middleware identify.tenant
|
*/

Route::domain(env('APP_DOMAIN', 'smartclick360-v2.test'))->group(function () {

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
            return view('landlord.dashboard');
        })->name('landlord.dashboard');

        // Gestão de Tenants
        Route::prefix('tenants')->name('landlord.tenants.')->group(function () {
            Route::get('/', [TenantManagementController::class, 'index'])->name('index');
            Route::get('/{code}', [TenantManagementController::class, 'show'])->name('show');
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

Route::domain('{slug}.' . env('APP_DOMAIN', 'smartclick360-v2.test'))->middleware('identify.tenant')->group(function () {

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
            return view('tenant.pages.dashboard.main');
        })->name('tenant.dashboard.main');

        // Configurações do Tenant
        Route::get('/settings', [TenantController::class, 'settings'])->name('tenant.settings');

        // Rotas específicas do módulo People
        Route::get('people/{code}/files', [\App\Http\Controllers\Tenant\PeopleController::class, 'showFiles'])->name('people.files');

        // Submódulos - rotas genéricas (DEVEM VIR ANTES das rotas de módulos)
        Route::post('{module}/{code}/{submodule}/reorder', [\App\Http\Controllers\Tenant\SubmoduleController::class, 'reorder'])->name('submodule.reorder');
        Route::get('{module}/{code}/{submodule}', [\App\Http\Controllers\Tenant\SubmoduleController::class, 'index'])->name('submodule.index');
        Route::get('{module}/{code}/{submodule}/create', [\App\Http\Controllers\Tenant\SubmoduleController::class, 'create'])->name('submodule.create');
        Route::post('{module}/{code}/{submodule}', [\App\Http\Controllers\Tenant\SubmoduleController::class, 'store'])->name('submodule.store');
        Route::get('{module}/{code}/{submodule}/{s_code}', [\App\Http\Controllers\Tenant\SubmoduleController::class, 'show'])->name('submodule.show');
        Route::get('{module}/{code}/{submodule}/{s_code}/edit', [\App\Http\Controllers\Tenant\SubmoduleController::class, 'edit'])->name('submodule.edit');
        Route::put('{module}/{code}/{submodule}/{s_code}', [\App\Http\Controllers\Tenant\SubmoduleController::class, 'update'])->name('submodule.update');
        Route::patch('{module}/{code}/{submodule}/{s_code}', [\App\Http\Controllers\Tenant\SubmoduleController::class, 'update'])->name('submodule.patch');
        Route::delete('{module}/{code}/{submodule}/{s_code}', [\App\Http\Controllers\Tenant\SubmoduleController::class, 'destroy'])->name('submodule.destroy');
        Route::patch('{module}/{code}/{submodule}/{s_code}/restore', [\App\Http\Controllers\Tenant\SubmoduleController::class, 'restore'])->name('submodule.restore');

        // Módulos - rotas genéricas (DEVEM VIR DEPOIS das rotas de submódulos)
        Route::get('{module}', [\App\Http\Controllers\Tenant\ModuleController::class, 'index'])->name('module.index');
        Route::get('{module}/create', [\App\Http\Controllers\Tenant\ModuleController::class, 'create'])->name('module.create');
        Route::post('{module}', [\App\Http\Controllers\Tenant\ModuleController::class, 'store'])->name('module.store');
        Route::get('{module}/{code}', [\App\Http\Controllers\Tenant\ModuleController::class, 'show'])->name('module.show');
        Route::get('{module}/{code}/edit', [\App\Http\Controllers\Tenant\ModuleController::class, 'edit'])->name('module.edit');
        Route::put('{module}/{code}', [\App\Http\Controllers\Tenant\ModuleController::class, 'update'])->name('module.update');
        Route::patch('{module}/{code}', [\App\Http\Controllers\Tenant\ModuleController::class, 'update'])->name('module.patch');
        Route::delete('{module}/{code}', [\App\Http\Controllers\Tenant\ModuleController::class, 'destroy'])->name('module.destroy');
        Route::post('{module}/reorder', [\App\Http\Controllers\Tenant\ModuleController::class, 'reorder'])->name('module.reorder');
        Route::patch('{module}/{code}/restore', [\App\Http\Controllers\Tenant\ModuleController::class, 'restore'])->name('module.restore');
    });
});
