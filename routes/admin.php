<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\AdminAuthenticatedSessionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\AnuncioController;
use App\Http\Controllers\Admin\SalidasAnticipadasController;
use App\Http\Controllers\Admin\IncidenciasController;
use App\Http\Controllers\Admin\CredencialesController;
use App\Http\Controllers\Admin\ContactosController;
use App\Http\Controllers\Admin\InscripcionesController;
use App\Http\Controllers\Admin\ReinscripcionController;
use App\Http\Controllers\PrefectosController;

// Sesión de administrador (login/logout)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AdminAuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('login', [AdminAuthenticatedSessionController::class, 'store'])->name('login.submit');
    });

    Route::post('logout', [AdminAuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// Área protegida de administrador
Route::prefix('admin')->name('admin.')->middleware([App\Http\Middleware\ValidateAdminAccess::class])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Alertas
    Route::post('/alertas/{id}/resolver', [AdminController::class, 'resolverAlerta'])->name('alertas.resolver');

    // Endpoints simples de datos (si se usan)
    Route::get('usuarios', [AdminController::class, 'getUsuarios'])->name('usuarios');
    Route::get('alumnos', [AdminController::class, 'getAlumnos'])->name('alumnos');

    // Inscripciones
    Route::prefix('inscripciones')->name('inscripciones.')->group(function () {
        Route::get('/inscripcion', [InscripcionesController::class, 'index'])->name('inscripcion');
        Route::get('/crear', [InscripcionesController::class, 'crear'])->name('crear');
        Route::get('/{id}/editar', [InscripcionesController::class, 'editar'])->name('editar');
        Route::post('/inscripcion', [InscripcionesController::class, 'store'])->name('inscripcion.store');
        Route::put('/inscripcion/{id}', [InscripcionesController::class, 'update'])->name('inscripcion.update');
        Route::delete('/inscripcion/{id}', [InscripcionesController::class, 'destroy'])->name('inscripcion.destroy');
        Route::post('/campo-extra/agregar', [InscripcionesController::class, 'agregarCampoExtra'])->name('campo_extra.agregar');
        Route::post('/campo-extra/eliminar', [InscripcionesController::class, 'eliminarCampoExtra'])->name('campo_extra.eliminar');
        
        // Reinscripción
        Route::get('/reinscripcion', [ReinscripcionController::class, 'index'])->name('reinscripcion');
        Route::get('/reinscripcion/crear', [ReinscripcionController::class, 'create'])->name('reinscripcion.crear');
        Route::post('/reinscripcion', [ReinscripcionController::class, 'store'])->name('reinscripcion.store');
        Route::get('/reinscripcion/{id}/editar', [ReinscripcionController::class, 'edit'])->name('reinscripcion.editar');
        Route::put('/reinscripcion/{id}', [ReinscripcionController::class, 'update'])->name('reinscripcion.update');
        Route::delete('/reinscripcion/{id}', [ReinscripcionController::class, 'destroy'])->name('reinscripcion.destroy');
        Route::post('/reinscripcion/cerrar', [ReinscripcionController::class, 'cerrarReinscripciones'])->name('reinscripcion.cerrar');
    });

    // Alumnos
    Route::prefix('alumnos')->name('alumnos.')->group(function () {
        Route::get('/pase-lista', fn () => Inertia::render('Admin/Alumnos/PaseLista'))->name('pase_lista');
        Route::get('/contactos', [AdminController::class, 'contactos'])->name('contactos');
        Route::get('/lista', fn () => Inertia::render('Admin/Alumnos/Lista'))->name('lista');
        Route::get('/lista-datos', [CredencialesController::class, 'listarAlumnos'])->name('lista_datos');
        Route::get('/contactos-datos', [AdminController::class, 'listarContactos'])->name('contactos_datos');
        Route::get('/{alumno}/perfil', [AdminController::class, 'perfilAlumno'])->name('perfil');
        Route::put('/{alumno}/actualizar', [AdminController::class, 'actualizarAlumno'])->name('actualizar');
        
        // Grupos
        Route::get('/grupos', [AdminController::class, 'grupos'])->name('grupos');
        Route::post('/grupos/crear', [AdminController::class, 'crearGrupo'])->name('grupos.crear');
        Route::put('/grupos/{id}', [AdminController::class, 'actualizarGrupo'])->name('grupos.actualizar');
        Route::delete('/grupos/{id}', [AdminController::class, 'eliminarGrupo'])->name('grupos.eliminar');
        Route::get('/grupos/{id}/alumnos', [AdminController::class, 'alumnosGrupo'])->name('grupos.alumnos');
        
        Route::get('/salud', fn () => Inertia::render('Admin/Alumnos/Salud'))->name('salud');
    });

    // Credenciales
    Route::prefix('credenciales')->name('credenciales.')->group(function () {
        Route::post('/generar', [CredencialesController::class, 'generar'])->name('generar');
    });

    // Contactos
    Route::prefix('contactos')->name('contactos.')->group(function () {
        Route::put('/{contacto}', [ContactosController::class, 'update'])->name('update');
        Route::patch('/{contacto}/principal', [ContactosController::class, 'marcarPrincipal'])->name('principal');
        Route::patch('/{contacto}/toggle-activo', [ContactosController::class, 'toggleActivo'])->name('toggle_activo');
        Route::delete('/{contacto}', [ContactosController::class, 'destroy'])->name('destroy');
    });

    // Agregar contacto a alumno
    Route::post('/alumnos/{alumno}/contactos', [ContactosController::class, 'store'])->name('alumnos.contactos.store');

    // Generar Docs
    Route::prefix('docs')->name('docs.')->group(function () {
        // Incidencias
        Route::get('/incidencias', [IncidenciasController::class, 'index'])->name('incidencias_index');
        Route::get('/incidencias/create', [IncidenciasController::class, 'create'])->name('incidencias_create');
        Route::post('/incidencias', [IncidenciasController::class, 'store'])->name('incidencias_store');
        Route::get('/incidencias/{incidencia}/preview', [IncidenciasController::class, 'preview'])->name('incidencias_preview');
        Route::post('/incidencias/{incidencia}/finalize', [IncidenciasController::class, 'finalize'])->name('incidencias_finalize');
        Route::delete('/incidencias/{id}/pdf', [IncidenciasController::class, 'borrarPDF'])->name('incidencia_borrar_pdf');
        Route::delete('/incidencias/{id}', [IncidenciasController::class, 'eliminar'])->name('incidencia_eliminar');
        
        // Salidas anticipadas
        Route::get('/autorizacion-salida', [SalidasAnticipadasController::class, 'create'])->name('autorizacion_salida');
        Route::get('/salidas', [SalidasAnticipadasController::class, 'index'])->name('salidas_index');
        Route::post('/salidas', [SalidasAnticipadasController::class, 'store'])->name('salidas_store');
        Route::get('/salidas/{salida}/pdf', [SalidasAnticipadasController::class, 'showPdf'])->name('salidas_pdf');
        Route::get('/salidas/{salida}/preview', [SalidasAnticipadasController::class, 'preview'])->name('salidas_preview');
        Route::post('/salidas/{salida}/finalize', [SalidasAnticipadasController::class, 'finalize'])->name('salidas_finalize');
        Route::delete('/salidas/{id}/pdf', [SalidasAnticipadasController::class, 'borrarPdf'])->name('salida_borrar_pdf');
        Route::delete('/salidas/{id}', [SalidasAnticipadasController::class, 'eliminar'])->name('salida_eliminar');
        Route::get('/alumnos/buscar', [SalidasAnticipadasController::class, 'buscarAlumno'])->name('buscar_alumno');
        Route::get('/alumnos/{alumno}/contactos', [SalidasAnticipadasController::class, 'contactosAlumno'])->name('contactos_alumno');
        Route::get('/credenciales', fn () => Inertia::render('Admin/Docs/Credenciales'))->name('credenciales');
    });

    // Gestión de Usuarios (solo Director y Sistemas)
    Route::prefix('usuarios')->name('usuarios.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\UsuariosController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\UsuariosController::class, 'store'])->name('store');
        Route::get('/{usuario}/edit', [\App\Http\Controllers\Admin\UsuariosController::class, 'edit'])->name('edit');
        Route::put('/{usuario}', [\App\Http\Controllers\Admin\UsuariosController::class, 'update'])->name('update');
        Route::delete('/{usuario}', [\App\Http\Controllers\Admin\UsuariosController::class, 'destroy'])->name('destroy');
    });

    // Otras secciones simples
    Route::get('/contrasenas', fn () => redirect()->route('admin.usuarios.index'))->name('contrasenas');
    Route::resource('formularios', \App\Http\Controllers\Admin\FormulariosController::class);
    Route::get('/anuncios', [AnuncioController::class, 'index'])->name('anuncios.index');
    Route::get('/anuncios/create', [AnuncioController::class, 'create'])->name('anuncios.create');
    Route::get('/anuncios/{anuncio}/edit', [AnuncioController::class, 'edit'])->name('anuncios.edit');

    // CRUD Anuncios (API Inertia)
    Route::post('/anuncios', [AnuncioController::class, 'store'])->name('anuncios.store');
    Route::put('/anuncios/{anuncio}', [AnuncioController::class, 'update'])->name('anuncios.update');
    Route::patch('/anuncios/{anuncio}', [AnuncioController::class, 'update'])->name('anuncios.patch');
    Route::delete('/anuncios/{anuncio}', [AnuncioController::class, 'destroy'])->name('anuncios.destroy');
});

// Área de Prefectos (rol limitado)
Route::prefix('prefectos')->name('prefectos.')->middleware([\App\Http\Middleware\ValidatePrefectoAccess::class])->group(function () {
    Route::get('/dashboard', [PrefectosController::class, 'dashboard'])->name('dashboard');

    // Alumnos: solo estas secciones
    Route::prefix('alumnos')->name('alumnos.')->group(function () {
        // Renderizar vistas propias de Prefectura para mantener restricciones y layout
        Route::get('/pase-lista', fn () => Inertia::render('Prefectos/Alumnos/PaseLista'))->name('pase_lista');
        Route::get('/lista', fn () => Inertia::render('Prefectos/Alumnos/Lista'))->name('lista');
        // Endpoint para obtener datos de alumnos (mismo controlador que admin)
        Route::get('/lista-datos', [CredencialesController::class, 'listarAlumnos'])->name('lista_datos');
        Route::get('/{alumno}/perfil', [PrefectosController::class, 'perfilAlumno'])->name('perfil');
    });
});
