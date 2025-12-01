<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Padres\DashboardController;

// Portal de Padres
Route::prefix('padres')->name('padres.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/asistencias', [DashboardController::class, 'asistencias'])->name('asistencias');
    Route::get('/solicitar-salida', [DashboardController::class, 'solicitarSalida'])->name('solicitar_salida');
    Route::get('/salidas-historial', [DashboardController::class, 'salidasHistorial'])->name('salidas_historial');
});
