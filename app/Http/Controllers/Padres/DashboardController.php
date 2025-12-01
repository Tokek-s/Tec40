<?php

namespace App\Http\Controllers\Padres;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\Asistencia;
use App\Models\SalidaAnticipada;
use App\Models\ContactoAlumno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Mostrar el dashboard del padre/tutor
     */
    public function index(): Response
    {
        $user = Auth::user();
        
        // Obtener el alumno asociado al correo del padre/tutor
        $contacto = ContactoAlumno::where('correo', $user->correo)
            ->where('es_activo', true)
            ->first();

        if (!$contacto || !$contacto->alumno_id) {
            return Inertia::render('Padres/SinAlumno');
        }

        // Cargar el alumno con su historial académico más reciente y archivo multimedia (foto)
        $alumno = Alumno::with(['historialActual.grupo', 'archivoMultimedia'])
            ->find($contacto->alumno_id);

        if (!$alumno) {
            return Inertia::render('Padres/SinAlumno');
        }
        
        // Obtener grado y grupo SOLO del historial académico actual
        $historialActual = $alumno->historialActual;
        $grado = 'Sin asignar';
        $grupo = 'Sin asignar';
        
        if ($historialActual && $historialActual->grupo) {
            $grado = $historialActual->grupo->grado;
            $grupo = $historialActual->grupo->clave;
        }
        
        // Obtener foto del alumno
        $fotoUrl = asset('images/avatar-default.svg');
        if ($alumno->archivoMultimedia && $alumno->archivoMultimedia->data_url) {
            $fotoUrl = $alumno->archivoMultimedia->data_url;
        }

        return Inertia::render('Padres/Dashboard', [
            'alumno' => [
                'id' => $alumno->id,
                'nombre_completo' => $alumno->nombre_completo,
                'matricula' => $alumno->matricula,
                'grado' => $grado,
                'grupo' => $grupo,
                'generacion' => $alumno->generacion,
                'estatus' => $alumno->estatus,
                'foto_url' => $fotoUrl,
            ],
        ]);
    }

    /**
     * Obtener asistencias del alumno por rango de fechas
     */
    public function asistencias(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        
        // Obtener el alumno asociado
        $contacto = ContactoAlumno::where('correo', $user->correo)
            ->where('es_activo', true)
            ->first();

        if (!$contacto || !$contacto->alumno_id) {
            return response()->json(['error' => 'No hay alumno asociado'], 404);
        }

        $fechaInicio = $request->query('fecha_inicio');
        $fechaFin = $request->query('fecha_fin');

        $query = Asistencia::where('alumno_id', $contacto->alumno_id)
            ->orderBy('fecha', 'desc');

        if ($fechaInicio) {
            $query->where('fecha', '>=', $fechaInicio);
        }

        if ($fechaFin) {
            $query->where('fecha', '<=', $fechaFin);
        }

        $asistencias = $query->get()->map(function($asistencia) {
            return [
                'id' => $asistencia->id,
                'fecha' => $asistencia->fecha->format('Y-m-d'), // Formatear la fecha correctamente
                'presente' => $asistencia->estado === 'presente',
                'justificada' => false, // Por ahora no hay campo de justificación
                'observaciones' => null,
            ];
        });

        return response()->json($asistencias);
    }

    /**
     * Mostrar formulario de solicitud de salida anticipada
     */
    public function solicitarSalida(): Response
    {
        $user = Auth::user();
        
        $contacto = ContactoAlumno::where('correo', $user->correo)
            ->where('es_activo', true)
            ->with('alumno')
            ->first();

        if (!$contacto || !$contacto->alumno) {
            return Inertia::render('Padres/SinAlumno');
        }

        return Inertia::render('Padres/SolicitarSalida', [
            'alumno' => [
                'id' => $contacto->alumno->id,
                'nombre_completo' => $contacto->alumno->nombre_completo,
                'matricula' => $contacto->alumno->matricula,
            ],
            'contacto' => [
                'id' => $contacto->id,
                'nombre_completo' => $contacto->nombre_completo,
                'parentesco' => $contacto->parentesco,
            ],
        ]);
    }

    /**
     * Ver historial de salidas anticipadas
     */
    public function salidasHistorial(): Response
    {
        $user = Auth::user();
        
        $contacto = ContactoAlumno::where('correo', $user->correo)
            ->where('es_activo', true)
            ->first();

        if (!$contacto || !$contacto->alumno_id) {
            return Inertia::render('Padres/SinAlumno');
        }

        $salidas = SalidaAnticipada::where('alumno_id', $contacto->alumno_id)
            ->with(['alumno', 'autorizadoPor', 'entregadoA'])
            ->orderBy('fecha', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->through(function($salida) {
                return [
                    'id' => $salida->id,
                    'fecha' => $salida->fecha,
                    'hora_salida' => $salida->hora_salida,
                    'motivo' => $salida->motivo,
                    'autorizado_por' => $salida->autorizadoPor?->nombre_completo,
                    'entregado_a' => $salida->entregadoA?->nombre_completo,
                    'estado' => $salida->estado ?? 'pendiente',
                ];
            });

        return Inertia::render('Padres/SalidasHistorial', [
            'salidas' => $salidas,
        ]);
    }
}
