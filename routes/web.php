<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/pricing', [PageController::class, 'pricing'])->name('pricing');
Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
Route::post('/check-slug', [RegisterController::class, 'checkSlug'])->name('register.checkSlug');
Route::post('/check-email', [RegisterController::class, 'checkEmail'])->name('register.checkEmail');
Route::get('/login', [LoginController::class, 'showForm'])->name('login');

// Teste do Dashboard (temporário)
Route::get('/dashboard-test', function () {
    return view('pages.dashboard-test');
})->name('dashboard.test');
