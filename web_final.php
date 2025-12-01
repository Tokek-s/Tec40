<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AdminAuthenticatedSessionController;
use App\Http\Controllers\Auth\TutorAuthenticatedSessionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TutorController;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Ruta temporal para probar DB
Route::get('/test-db', function() {
    try {
        $connection = DB::connection();
        $connection->getPdo();
        
        echo "<h1>✅ Conexión a Base de Datos OK</h1>";
        echo "<p>Base de datos: " . $connection->getDatabaseName() . "</p>";
        
        // Verificar tabla usuarios
        $usuarios = DB::table('usuarios')->count();
        echo "<p>Usuarios en la tabla: $usuarios</p>";
        
        // Mostrar algunos usuarios
        $users = DB::table('usuarios')->limit(3)->get(['id', 'nombre_s', 'apellido_paterno', 'correo', 'rol']);
        echo "<h3>Usuarios de ejemplo:</h3>";
        foreach ($users as $user) {
            echo "<p>ID: {$user->id}, Nombre: {$user->nombre_s} {$user->apellido_paterno}, Email: {$user->correo}, Role: {$user->rol}</p>";
        }
        
    } catch (Exception $e) {
        echo "<h1>❌ Error de conexión</h1>";
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
});

// Admin Authentication Routes
Route::prefix('admin')->group(function () {
    Route::get('login', [AdminAuthenticatedSessionController::class, 'create'])->name('admin.login');
    Route::post('login', [AdminAuthenticatedSessionController::class, 'store'])->name('admin.login.store');
    Route::post('logout', [AdminAuthenticatedSessionController::class, 'destroy'])->name('admin.logout');
    
    Route::middleware('auth')->group(function () {
        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // API Routes para admin
        Route::get('api/usuarios', [AdminController::class, 'getUsuarios'])->name('api.usuarios');
        Route::get('api/alumnos', [AdminController::class, 'getAlumnos'])->name('api.alumnos');
        Route::get('api/anuncios', [AdminController::class, 'getAnuncios'])->name('api.anuncios');
    });
});

// Tutor Authentication Routes  
Route::prefix('tutor')->group(function () {
    Route::get('login', [TutorAuthenticatedSessionController::class, 'create'])->name('tutor.login');
    Route::post('login', [TutorAuthenticatedSessionController::class, 'store'])->name('tutor.login.store');
    Route::post('logout', [TutorAuthenticatedSessionController::class, 'destroy'])->name('tutor.logout');
    
    Route::middleware('auth')->group(function () {
        Route::get('dashboard', [TutorController::class, 'dashboard'])->name('tutor.dashboard');
    });
});
