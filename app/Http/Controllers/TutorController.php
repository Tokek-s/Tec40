<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Anuncio;
use App\Models\Asistencia;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class TutorController extends Controller
{
    public function dashboard()
    {
        $alumnoId = Session::get('tutor_alumno_id');
        
        if (!$alumnoId) {
            return redirect()->route('tutor.login');
        }

        // Cargar el alumno con las mismas relaciones que en la lista de alumnos
        $alumno = Alumno::with(['salud', 'contactoPrincipal', 'historialActual.grupo'])
            ->find($alumnoId);

        if (!$alumno) {
            return redirect()->route('tutor.login');
        }

        $historialActual = $alumno->historialActual;
        $salud = $alumno->salud;
        $contacto = $alumno->contactoPrincipal;
        
        // Construir el objeto de datos exactamente como en listarAlumnos
        $alumnoData = [
            'id' => $alumno->id,
            'matricula' => $alumno->matricula,
            'nombre_completo' => $alumno->nombre_completo,
            'curp' => $alumno->curp,
            'grado' => $historialActual && $historialActual->grupo ? $historialActual->grupo->grado : null,
            'grupo' => $historialActual && $historialActual->grupo ? $historialActual->grupo->clave : null,
            'turno' => $historialActual && $historialActual->grupo ? $historialActual->grupo->turno : null,
            'generacion' => $alumno->generacion,
            'estatus' => $historialActual->estatus ?? $alumno->estatus,
            'tipo_sangre' => $salud ? $salud->tipo_sangre : null,
            'foto_url' => $alumno->foto_url,
            'alergias' => $salud ? $salud->alergias : null,
            'telefono_tutor' => $contacto ? $contacto->telefono : null,
        ];

        return Inertia::render('Tutor/Dashboard', [
            'alumno' => $alumnoData,
        ]);
    }

    public function asistencias()
    {
        $alumnoId = Session::get('tutor_alumno_id');
        
        if (!$alumnoId) {
            return redirect()->route('tutor.login');
        }

        // Cargar el alumno con información básica
        $alumno = Alumno::with(['historialActual.grupo'])
            ->find($alumnoId);

        if (!$alumno) {
            return redirect()->route('tutor.login');
        }

        $historialActual = $alumno->historialActual;

        $alumnoData = [
            'id' => $alumno->id,
            'matricula' => $alumno->matricula,
            'nombre_completo' => $alumno->nombre_completo,
            'grado' => $historialActual && $historialActual->grupo ? $historialActual->grupo->grado : null,
            'grupo' => $historialActual && $historialActual->grupo ? $historialActual->grupo->clave : null,
        ];

        return Inertia::render('Tutor/Asistencias', [
            'alumno' => $alumnoData,
        ]);
    }

    public function getAsistencias()
    {
        $alumnoId = Session::get('tutor_alumno_id');
        
        if (!$alumnoId) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        $asistencias = Asistencia::where('alumno_id', $alumnoId)
            ->orderBy('fecha', 'desc')
            ->get()
            ->map(function ($asistencia) {
                // Convertir el campo 'estado' ENUM a los campos booleanos que espera el frontend
                // Valores posibles: 'presente', 'falta', 'retardo', 'justificado'
                $presente = in_array($asistencia->estado, ['presente', 'retardo']);
                $justificada = $asistencia->estado === 'justificado';
                
                return [
                    'id' => $asistencia->id,
                    'fecha' => $asistencia->fecha,
                    'presente' => $presente,
                    'justificada' => $justificada,
                    'observaciones' => $asistencia->observaciones ?? null,
                    'registrado_por' => $asistencia->registrado_por,
                ];
            });

        return response()->json($asistencias);
    }

    public function anuncios()
    {
        $alumnoId = Session::get('tutor_alumno_id');
        
        if (!$alumnoId) {
            return redirect()->route('tutor.login');
        }

        // Cargar información básica del alumno
        $alumno = Alumno::find($alumnoId);

        if (!$alumno) {
            return redirect()->route('tutor.login');
        }

        // Obtener todos los anuncios activos ordenados por fecha descendente
        $anuncios = Anuncio::where('activo', true)
            ->orderBy('fecha', 'desc')
            ->get()
            ->map(function ($anuncio) {
                return [
                    'id' => $anuncio->id,
                    'titulo' => $anuncio->titulo,
                    'contenido' => $anuncio->contenido,
                    'fecha' => $anuncio->fecha,
                    'ruta_imagen' => $anuncio->ruta_imagen,
                    'image_url' => $anuncio->image_url,
                ];
            });

        $alumnoData = [
            'id' => $alumno->id,
            'nombre_completo' => $alumno->nombre_completo,
            'matricula' => $alumno->matricula,
        ];

        return Inertia::render('Tutor/Anuncios', [
            'anuncios' => $anuncios,
            'alumno' => $alumnoData,
        ]);
    }
}

