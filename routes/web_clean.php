<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AdminAuthenticatedSessionController;
use App\Http\Controllers\Auth\TutorAuthenticatedSessionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TutorController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Rutas de autenticación de administrador
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AdminAuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('login', [AdminAuthenticatedSessionController::class, 'store'])->name('login.submit');
    });

    Route::middleware('auth')->group(function () {
        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::post('logout', [AdminAuthenticatedSessionController::class, 'destroy'])->name('logout');
    });
});

// Rutas de autenticación de tutor
Route::prefix('tutor')->name('tutor.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [TutorAuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('login', [TutorAuthenticatedSessionController::class, 'store'])->name('login.submit');
    });

    Route::middleware('tutor')->group(function () {
        Route::get('dashboard', [TutorController::class, 'dashboard'])->name('dashboard');
        Route::post('logout', [TutorAuthenticatedSessionController::class, 'destroy'])->name('logout');
    });
});

// Rutas de inscripciones
Route::get('/inscripciones/primero', function () {
    return inertia('Inscripciones/Primero');
})->name('inscripciones.primero');

Route::get('/inscripciones/segundo', function () {
    return inertia('Inscripciones/Segundo');
})->name('inscripciones.segundo');

Route::get('/inscripciones/tercero', function () {
    return inertia('Inscripciones/Tercero');
})->name('inscripciones.tercero');
