<?php

namespace App\Jobs;

use App\Models\Alumno;
use App\Models\Grupo;
use App\Models\HistorialAcademico;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class AsignarGruposAutomaticamente implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Obtener alumnos sin grupo asignado
        $alumnosSinGrupo = Alumno::whereDoesntHave('historialActual', function ($query) {
            $query->whereNotNull('grupo_id');
        })->get();

        if ($alumnosSinGrupo->isEmpty()) {
            return; // No hay alumnos sin grupo
        }

        // Agrupar por grado y turno
        $alumnosPorGradoTurno = $alumnosSinGrupo->groupBy(function ($alumno) {
            $historial = $alumno->historialActual;
            if ($historial) {
                return $historial->grado . '_' . $historial->turno;
            }
            return 'sin_historial';
        });

        foreach ($alumnosPorGradoTurno as $key => $alumnos) {
            if ($key === 'sin_historial') {
                continue;
            }

            [$grado, $turno] = explode('_', $key);

            // Obtener grupos disponibles
            $grupos = Grupo::where('grado', $grado)
                ->where('turno', $turno)
                ->where('activo', 1)
                ->get();

            if ($grupos->isEmpty()) {
                continue; // No hay grupos disponibles para este grado/turno
            }

            // Asignar alumnos a grupos usando el algoritmo equitativo
            $this->asignarAlumnosAGrupos($alumnos, $grupos, $grado, $turno);

            // Resolver alerta si existía
            $this->resolverAlerta($grado, $turno);
        }
    }

    private function asignarAlumnosAGrupos($alumnos, $grupos, string $grado, string $turno): void
    {
        // Separar por sexo
        $hombres = $alumnos->where('sexo', 'M')->values();
        $mujeres = $alumnos->where('sexo', 'F')->values();

        // Calcular distribución ideal
        $totalAlumnos = $alumnos->count();
        $numGrupos = $grupos->count();
        $alumnosPorGrupo = ceil($totalAlumnos / $numGrupos);

        // Obtener conteos actuales por grupo
        $gruposConConteos = $grupos->map(function ($grupo) {
            $totalAlumnos = HistorialAcademico::where('grupo_id', $grupo->id)
                ->whereIn('estado', ['inscrito', 'cursando'])
                ->count();
            
            $hombres = HistorialAcademico::where('grupo_id', $grupo->id)
                ->whereIn('estado', ['inscrito', 'cursando'])
                ->whereHas('alumno', function ($q) {
                    $q->where('sexo', 'M');
                })
                ->count();
            
            $mujeres = $totalAlumnos - $hombres;
            
            return [
                'grupo' => $grupo,
                'total' => $totalAlumnos,
                'hombres' => $hombres,
                'mujeres' => $mujeres,
                'disponibles' => 45 - $totalAlumnos,
            ];
        })->filter(function ($info) {
            return $info['disponibles'] > 0;
        })->sortBy(function ($info) {
            return $info['total']; // Llenar primero los grupos con menos alumnos
        });

        // Asignar hombres
        foreach ($hombres as $alumno) {
            $grupoSeleccionado = $gruposConConteos->sortByDesc(function ($info) {
                // Priorizar grupos con más mujeres (para equilibrar)
                return ($info['mujeres'] - $info['hombres']) * 1000 + (45 - $info['total']);
            })->first();

            if (!$grupoSeleccionado || $grupoSeleccionado['disponibles'] <= 0) {
                break; // No hay más espacio
            }

            $this->asignarAlumno($alumno, $grupoSeleccionado['grupo']);

            // Actualizar conteos
            $grupoSeleccionado['hombres']++;
            $grupoSeleccionado['total']++;
            $grupoSeleccionado['disponibles']--;
        }

        // Asignar mujeres
        foreach ($mujeres as $alumno) {
            $grupoSeleccionado = $gruposConConteos->sortByDesc(function ($info) {
                // Priorizar grupos con más hombres (para equilibrar)
                return ($info['hombres'] - $info['mujeres']) * 1000 + (45 - $info['total']);
            })->first();

            if (!$grupoSeleccionado || $grupoSeleccionado['disponibles'] <= 0) {
                break; // No hay más espacio
            }

            $this->asignarAlumno($alumno, $grupoSeleccionado['grupo']);

            // Actualizar conteos
            $grupoSeleccionado['mujeres']++;
            $grupoSeleccionado['total']++;
            $grupoSeleccionado['disponibles']--;
        }
    }

    private function asignarAlumno(Alumno $alumno, Grupo $grupo): void
    {
        HistorialAcademico::where('alumno_id', $alumno->id)
            ->whereNull('grupo_id')
            ->update(['grupo_id' => $grupo->id]);
    }

    private function resolverAlerta(string $grado, string $turno): void
    {
        DB::table('alertas_sistema')
            ->where('tipo', 'sobrecupo')
            ->where('grado', $grado)
            ->where('turno', $turno)
            ->where('resuelta', 0)
            ->update([
                'resuelta' => 1,
                'fecha_resolucion' => now(),
                'nota_resolucion' => 'Grupos asignados automáticamente después de la creación de nuevos grupos.',
            ]);
    }
}
