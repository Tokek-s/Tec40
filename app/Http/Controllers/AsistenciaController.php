<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Asistencia;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Inertia\Response;

class AsistenciaController extends Controller
{
    // Opcional: página, pero usamos API JSON para Vue
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $grupo = $request->get('grupo');
        $turno = $request->get('turno');
        $fecha = $request->get('fecha', Carbon::today()->toDateString());

        Log::info('Asistencias.index filtros', compact('q','grupo','turno','fecha'));

        $builder = Alumno::query()->with('historialActual.grupo');

        // Filtro por grupo/grado usando la relación historialActual.grupo
        if (!empty($grupo)) {
            $builder->whereHas('historialActual.grupo', function ($query) use ($grupo) {
                // Intentar extraer grado y clave del formato "1A", "2B", etc.
                if (preg_match('/^(\d+)([A-F])$/', $grupo, $matches)) {
                    // Formato exacto: grado + clave (ej: "1A", "2B")
                    $grado = $matches[1];
                    $clave = $matches[2];
                    $query->where('grado', $grado)->where('clave', $clave);
                } elseif (is_numeric($grupo)) {
                    // Solo número: filtrar por grado
                    $query->where('grado', $grupo);
                } else {
                    // Solo letra: filtrar por clave
                    $query->where('clave', $grupo);
                }
            });
        }

        // Filtro por turno usando la relación historialActual.grupo
        if (!empty($turno)) {
            $builder->whereHas('historialActual.grupo', function ($query) use ($turno) {
                $query->where('turno', $turno);
            });
        }

        // Búsqueda libre por nombre/matrícula/curp
        if (!empty($q)) {
            $builder->where(function ($w) use ($q) {
                $w->where('nombre_s', 'like', "%$q%")
                  ->orWhere('apellido_paterno', 'like', "%$q%")
                  ->orWhere('apellido_materno', 'like', "%$q%")
                  ->orWhere('matricula', 'like', "%$q%")
                  ->orWhere('curp', 'like', "%$q%");
            });
        }

        // Ordenar alfabéticamente por apellidos y luego por nombre
        $alumnos = $builder
            ->orderBy('apellido_paterno', 'asc')
            ->orderBy('apellido_materno', 'asc')
            ->orderBy('nombre_s', 'asc')
            ->limit(200)
            ->get();

        $asistenciasData = [];
        if ($alumnos->isNotEmpty()) {
            $asistenciasData = Asistencia::whereDate('fecha', $fecha)
                ->whereIn('alumno_id', $alumnos->pluck('id'))
                ->get()
                ->keyBy('alumno_id');
        }

        $data = $alumnos->map(function ($a) use ($asistenciasData) {
            // Formato: Apellido Paterno + Apellido Materno + Nombre(s)
            $nombre = trim($a->apellido_paterno . ' ' . $a->apellido_materno . ' ' . $a->nombre_s);

            $matricula = $a->matricula ?? '';
            
            // Obtener grupo desde historialActual.grupo
            $grupoInfo = '';
            if ($a->historialActual && $a->historialActual->grupo) {
                $grupo = $a->historialActual->grupo;
                $grado = $grupo->grado;
                $clave = $grupo->clave;
                $turnoLetra = $grupo->turno === 'Matutino' ? 'M' : 'V';
                $grupoInfo = "{$grado}° {$clave} {$turnoLetra}";
            }

            $asistencia = $asistenciasData->get($a->id);
            $tieneFalta = $asistencia !== null && $asistencia->estado === 'falta';
            $justificado = $asistencia !== null && $asistencia->estado === 'justificado';

            return [
                'id' => $a->id,
                'nombre_completo' => $nombre,
                'matricula' => $matricula,
                'grupo' => $grupoInfo,
                'tiene_falta' => $tieneFalta,
                'justificado' => $justificado,
                'foto_url' => $a->foto_url,
            ];
        });

        $response = [
            'fecha' => $fecha,
            'alumnos' => $data,
        ];
        Log::info('Asistencias.index respuesta', ['count' => $data->count()]);
        return response()->json($response);
    }

    public function marcarFalta(Request $request)
    {
        $validated = $request->validate([
            'alumno_id' => ['required', 'integer', 'exists:alumnos,id'],
            'fecha' => ['nullable', 'date'],
        ]);

        $fecha = $validated['fecha'] ?? Carbon::today()->toDateString();
        try {
            $fecha = Carbon::parse($fecha)->toDateString();
        } catch (\Throwable $e) {
            $fecha = Carbon::today()->toDateString();
        }

        Log::info('Asistencias.marcarFalta', ['alumno_id' => $validated['alumno_id'], 'fecha' => $fecha]);
        
        // Obtener grupo_id del alumno desde historialActual
        $alumno = Alumno::find($validated['alumno_id']);
        $grupoId = $alumno && $alumno->historialActual ? $alumno->historialActual->grupo_id : null;

        $row = Asistencia::updateOrCreate(
            ['alumno_id' => $validated['alumno_id'], 'fecha' => $fecha],
            [
                'estado' => 'falta',
                'grupo_id' => $grupoId,
                'registrado_por' => auth()->user()->id,
            ]
        );
        
        Log::info('Asistencias.marcarFalta ok', ['id' => $row->id]);
        return response()->json(['ok' => true, 'falta' => true, 'id' => $row->id, 'fecha' => $fecha]);
    }

    public function quitarFalta(Request $request)
    {
        $validated = $request->validate([
            'alumno_id' => ['required', 'integer', 'exists:alumnos,id'],
            'fecha' => ['nullable', 'date'],
        ]);

        $fecha = $validated['fecha'] ?? Carbon::today()->toDateString();
        try {
            $fecha = Carbon::parse($fecha)->toDateString();
        } catch (\Throwable $e) {
            $fecha = Carbon::today()->toDateString();
        }

        Log::info('Asistencias.quitarFalta', ['alumno_id' => $validated['alumno_id'], 'fecha' => $fecha]);
        $deleted = Asistencia::where('alumno_id', $validated['alumno_id'])
            ->whereDate('fecha', $fecha)
            ->delete();
        Log::info('Asistencias.quitarFalta ok', ['deleted' => $deleted]);
        return response()->json(['ok' => true, 'falta' => false, 'deleted' => $deleted, 'fecha' => $fecha]);
    }

    public function marcarJustificado(Request $request)
    {
        $validated = $request->validate([
            'alumno_id' => ['required', 'integer', 'exists:alumnos,id'],
            'fecha' => ['nullable', 'date'],
        ]);

        $fecha = $validated['fecha'] ?? Carbon::today()->toDateString();
        try {
            $fecha = Carbon::parse($fecha)->toDateString();
        } catch (\Throwable $e) {
            $fecha = Carbon::today()->toDateString();
        }

        Log::info('Asistencias.marcarJustificado', ['alumno_id' => $validated['alumno_id'], 'fecha' => $fecha]);
        
        // Obtener grupo_id del alumno desde historialActual
        $alumno = Alumno::find($validated['alumno_id']);
        $grupoId = $alumno && $alumno->historialActual ? $alumno->historialActual->grupo_id : null;

        $row = Asistencia::updateOrCreate(
            ['alumno_id' => $validated['alumno_id'], 'fecha' => $fecha],
            [
                'estado' => 'justificado',
                'grupo_id' => $grupoId,
                'registrado_por' => auth()->user()->id,
            ]
        );
        
        Log::info('Asistencias.marcarJustificado ok', ['id' => $row->id]);
        return response()->json(['ok' => true, 'justificado' => true, 'id' => $row->id, 'fecha' => $fecha]);
    }

    public function pasarLista(Request $request)
    {
        $validated = $request->validate([
            'fecha' => ['required', 'date'],
            'alumnos' => ['required', 'array'],
            'alumnos.*.id' => ['required', 'integer', 'exists:alumnos,id'],
            'alumnos.*.estado' => ['required', 'in:presente,falta,retardo,justificado'],
        ]);

        $fecha = Carbon::parse($validated['fecha'])->toDateString();
        $registradoPor = auth()->user()->id;
        $guardados = 0;

        Log::info('Asistencias.pasarLista', [
            'fecha' => $fecha,
            'total_alumnos' => count($validated['alumnos']),
            'registrado_por' => $registradoPor
        ]);

        foreach ($validated['alumnos'] as $alumnoData) {
            $alumno = Alumno::find($alumnoData['id']);
            $grupoId = $alumno && $alumno->historialActual ? $alumno->historialActual->grupo_id : null;

            Asistencia::updateOrCreate(
                [
                    'alumno_id' => $alumnoData['id'],
                    'fecha' => $fecha
                ],
                [
                    'estado' => $alumnoData['estado'],
                    'grupo_id' => $grupoId,
                    'registrado_por' => $registradoPor,
                ]
            );
            
            $guardados++;
        }

        Log::info('Asistencias.pasarLista ok', ['guardados' => $guardados]);

        return response()->json([
            'ok' => true,
            'mensaje' => "Lista pasada exitosamente. Se guardaron {$guardados} registros de asistencia.",
            'guardados' => $guardados,
            'fecha' => $fecha
        ]);
    }
}
