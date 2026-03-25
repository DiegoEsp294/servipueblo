<?php

use App\Http\Controllers\AiChatController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\WorkerController;
use App\Http\Controllers\WorkerApplicationController;
use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ── Rutas públicas ───────────────────────────────────────────────────────────

Route::get('/', [WorkerController::class, 'index'])->name('home');
Route::get('/trabajadores', [WorkerController::class, 'index'])->name('workers.index');
Route::get('/trabajadores/{worker:slug}', [WorkerController::class, 'show'])->name('workers.show');
Route::get('/trabajadores/{worker:slug}/whatsapp', [WorkerController::class, 'trackWhatsapp'])->name('workers.whatsapp');
Route::post('/trabajadores/{worker:slug}/compartir', [WorkerController::class, 'trackShare'])->name('workers.share');
Route::post('/chat', [AiChatController::class, 'chat'])->name('ai.chat')->middleware('throttle:20,60');
Route::get('/categoria/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

// ── Registro de trabajador (público) ─────────────────────────────────────────

Route::get('/registrate-como-trabajador', [WorkerApplicationController::class, 'create'])->name('workers.apply');
Route::post('/registrate-como-trabajador', [WorkerApplicationController::class, 'store'])->middleware('throttle:5,60');

Route::post('/trabajadores/{worker}/calificar', [RatingController::class, 'store'])
    ->name('ratings.store')
    ->middleware('throttle:20,60');

// ── Google OAuth ────────────────────────────────────────────────────────────

Route::get('/auth/google', [SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// ── Verificación de correo ───────────────────────────────────────────────────

Route::get('/verificar', [EmailVerificationController::class, 'showEmailForm'])->name('verification.email-form');
Route::post('/verificar', [EmailVerificationController::class, 'sendCode'])->name('verification.send-code')->middleware('throttle:5,10');
Route::get('/verificar/codigo', [EmailVerificationController::class, 'showCodeForm'])->name('verification.code-form');
Route::post('/verificar/codigo', [EmailVerificationController::class, 'verifyCode'])->name('verification.verify-code');
Route::post('/verificar/cerrar', [EmailVerificationController::class, 'logout'])->name('verification.logout');

// ── Auth (Login / Logout) ────────────────────────────────────────────────────

Auth::routes(['register' => false, 'reset' => false, 'verify' => false]);

// ── Rutas Admin (protegidas con auth) ────────────────────────────────────────

Route::prefix('admin')->middleware('auth')->name('admin.')->group(function () {

    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Trabajadores
    Route::resource('workers', Admin\WorkerController::class)->except(['show']);
    Route::post('workers/{worker}/approve', [Admin\WorkerController::class, 'approve'])->name('workers.approve');

    // Categorías
    Route::get('categories', [Admin\CategoryController::class, 'index'])->name('categories.index');
    Route::post('categories', [Admin\CategoryController::class, 'store'])->name('categories.store');
    Route::delete('categories/{category}', [Admin\CategoryController::class, 'destroy'])->name('categories.destroy');

    // Calificaciones
    Route::get('ratings', [Admin\RatingController::class, 'index'])->name('ratings.index');
    Route::delete('ratings/{rating}', [Admin\RatingController::class, 'destroy'])->name('ratings.destroy');

    // Métricas
    Route::get('metrics', [Admin\MetricsController::class, 'index'])->name('metrics.index');
    Route::get('metrics/trabajador/{worker}', [Admin\MetricsController::class, 'worker'])->name('metrics.worker');
});

