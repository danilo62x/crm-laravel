<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MarketingController;
use Illuminate\Support\Facades\Route;

// ─── Public site ─────────────────────────────────────────────
Route::get('/', [MarketingController::class, 'landing'])->name('home');
Route::get('/contato', [MarketingController::class, 'contact'])->name('contact');

// ─── Auth (guest only) ───────────────────────────────────────
Route::middleware('guest.mock')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/registro', [AuthController::class, 'register'])->name('register.submit');
    Route::get('/esqueci-senha', [AuthController::class, 'showForgotPassword'])->name('password.request');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ─── App (auth-gated, mock) ──────────────────────────────────
Route::middleware('auth.mock')->group(function () {
    Route::get('/app', [AppController::class, 'dashboard'])->name('dashboard');
    Route::get('/app/pipeline', [AppController::class, 'pipeline'])->name('app.pipeline');
    Route::get('/app/contatos', [AppController::class, 'contacts'])->name('app.contacts');
    Route::get('/app/contatos/{id}', [AppController::class, 'contactDetail'])->whereNumber('id')->name('app.contact');
    Route::get('/app/empresas', [AppController::class, 'companies'])->name('app.companies');
    Route::get('/app/negocios', [AppController::class, 'deals'])->name('app.deals');
    Route::get('/app/atividades', [AppController::class, 'activities'])->name('app.activities');
    Route::get('/app/relatorios', [AppController::class, 'reports'])->name('app.reports');
    Route::get('/app/configuracoes', [AppController::class, 'settings'])->name('app.settings');
});

// ─── Fallback 404 ────────────────────────────────────────────
Route::fallback(fn () => response()->view('pages.not-found', ['pageTitle' => '404'], 404));
