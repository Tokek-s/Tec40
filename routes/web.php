<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TutorController;
use App\Http\Controllers\Auth\TutorAuthenticatedSessionController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\FormularioInscripcionController;

// Página pública principal (Home)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Alias de login genérico → login de admin (protege cuando algún middleware usa route('login'))
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

// Rutas de autenticación/navegación del portal de tutores
Route::prefix('tutor')->name('tutor.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [TutorAuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('login', [TutorAuthenticatedSessionController::class, 'store'])->name('login.submit');
    });

    // Rutas protegidas del portal de tutores
    Route::middleware([App\Http\Middleware\ValidateTutorAccess::class])->group(function () {
        Route::get('dashboard', [TutorController::class, 'dashboard'])->name('dashboard');
        Route::get('asistencias', [TutorController::class, 'asistencias'])->name('asistencias');
        Route::get('asistencias/data', [TutorController::class, 'getAsistencias'])->name('asistencias.data');
        Route::get('anuncios', [TutorController::class, 'anuncios'])->name('anuncios');
        Route::post('logout', [TutorAuthenticatedSessionController::class, 'destroy'])->name('logout');
    });
});

// Formularios públicos de inscripción
Route::get('/inscripcion/{tipo}', [FormularioInscripcionController::class, 'mostrar'])
    ->whereIn('tipo', ['primero', 'segundo', 'tercero', 'reinscripcion'])
    ->name('formulario.inscripcion');
Route::post('/inscripcion/guardar', [FormularioInscripcionController::class, 'guardar'])
    ->name('formulario.inscripcion.guardar');

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/padres.php';

// API Pase de Lista (permitido para roles: Sistemas, Direccion, Prefecto(s))
Route::middleware(['auth', App\Http\Middleware\ValidatePaseListaAccess::class])->group(function () {
    Route::get('/asistencias', [\App\Http\Controllers\AsistenciaController::class, 'index'])->name('asistencias.index');
    Route::post('/asistencias/falta', [\App\Http\Controllers\AsistenciaController::class, 'marcarFalta'])->name('asistencias.marcar');
    Route::delete('/asistencias/falta', [\App\Http\Controllers\AsistenciaController::class, 'quitarFalta'])->name('asistencias.quitar');
    Route::post('/asistencias/justificado', [\App\Http\Controllers\AsistenciaController::class, 'marcarJustificado'])->name('asistencias.justificado');
    Route::post('/asistencias/pasar-lista', [\App\Http\Controllers\AsistenciaController::class, 'pasarLista'])->name('asistencias.pasar-lista');
});

// Ruta de depuración local para ver cuestionarios normalizados
if (app()->environment('local')) {
    Route::get('/debug/cuestionarios', function () {
        try {
            if (!\Illuminate\Support\Facades\DB::getSchemaBuilder()->hasTable('fechas_cuestionarios')) {
                return response()->json(['error' => 'Tabla no existe'], 404);
            }
            $data = \App\Models\FechaCuestionario::query()->get()->map(function ($c) {
                return [
                    'id' => $c->id,
                    'titulo' => $c->titulo,
                    'descripcion' => $c->descripcion,
                    'fecha_inicio' => optional($c->fecha_inicio)->format('Y-m-d'),
                    'fecha_fin' => optional($c->fecha_fin)->format('Y-m-d'),
                    'activo' => (bool) $c->activo,
                    'primero_activo' => (bool) $c->primero_activo,
                    'segundo_activo' => (bool) $c->segundo_activo,
                    'tercero_activo' => (bool) $c->tercero_activo,
                ];
            });
            return response()->json($data);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    });
}
