<?php

namespace App\Http\Controllers;

use App\Models\Anuncio;
use App\Models\Alumno;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PrefectosController extends Controller
{
    // El middleware ya está aplicado en las rutas (routes/admin.php)
    // No es necesario duplicarlo aquí

    public function dashboard(): Response
    {
        $user = auth()->user();

        $anuncios_recientes = Anuncio::orderBy('fecha', 'desc')
            ->limit(6)
            ->get()
            ->map(fn($a) => [
                'id' => $a->id,
                'titulo' => $a->titulo,
                'contenido' => $a->contenido,
                'fecha' => optional($a->fecha)->format('d/m/Y'),
                'activo' => (bool) $a->activo,
            ]);

        // Prefecto: solo valores (tarjetas) y anuncios. Nada de edición ni accesos a CRUD de anuncios.
        $stats = [
            'total_alumnos' => null,
            'docentes_activos' => null,
            'grupos' => null,
            'total_anuncios' => $anuncios_recientes->count(),
            'cuestionarios_activos' => null,
        ];

        return Inertia::render('Prefectos/Dashboard', [
            'user' => [
                'id' => $user->id,
                'nombre_completo' => $user->nombre_completo,
                'correo' => $user->correo,
                'rol' => $user->rol,
            ],
            'stats' => $stats,
            'anuncios_recientes' => $anuncios_recientes,
        ]);
    }

    public function perfilAlumno(Alumno $alumno): Response
    {
        // Cargar relaciones necesarias
        $alumno->load([
            'salud',
            'contactos',
            'historialActual.grupo'
        ]);

        // Obtener datos del historial actual
        $historialActual = $alumno->historialActual;

        // Preparar datos del alumno
        $alumnoData = [
            'id' => $alumno->id,
            'nombre_s' => $alumno->nombre_s,
            'apellido_paterno' => $alumno->apellido_paterno,
            'apellido_materno' => $alumno->apellido_materno,
            'nombre_completo' => $alumno->nombre_completo,
            'fecha_nacimiento' => $alumno->fecha_nacimiento ? $alumno->fecha_nacimiento->format('Y-m-d') : null,
            'curp' => $alumno->curp,
            'sexo' => $alumno->sexo,
            'matricula' => $alumno->matricula,
            // Datos del historial actual
            'grado' => $historialActual && $historialActual->grupo ? $historialActual->grupo->grado : null,
            'grupo' => $historialActual && $historialActual->grupo ? $historialActual->grupo->clave : null,
            'turno' => $historialActual && $historialActual->grupo ? $historialActual->grupo->turno : null,
            'generacion' => $alumno->generacion,
            'estatus' => $historialActual->estatus ?? $alumno->estatus,
            'foto_url' => $alumno->foto_url,
            'salud' => $alumno->salud ? [
                'tipo_sangre' => $alumno->salud->tipo_sangre,
                'alergias' => $alumno->salud->alergias,
                'enfermedades_cronicas' => $alumno->salud->enfermedades_cronicas,
            ] : null,
            'contactos' => $alumno->contactos->map(function ($contacto) {
                return [
                    'id' => $contacto->id,
                    'nombre_completo' => $contacto->nombre_completo,
                    'parentesco' => $contacto->parentesco,
                    'telefono' => $contacto->telefono,
                    'correo' => $contacto->correo,
                    'autorizado_recoger' => (bool) $contacto->autorizado_recoger,
                ];
            }),
        ];

        return Inertia::render('Prefectos/Alumnos/Perfil', [
            'alumno' => $alumnoData
        ]);
    }
}

