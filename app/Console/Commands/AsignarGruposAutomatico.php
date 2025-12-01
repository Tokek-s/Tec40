<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Alumno;
use App\Models\Grupo;
use App\Models\HistorialAcademico;

class AsignarGruposAutomatico extends Command
{
    protected $signature = 'alumnos:asignar-grupos';
    protected $description = 'Asigna automáticamente grupos a alumnos que no tienen grupo asignado';

    public function handle()
    {
        $this->info('Buscando alumnos sin grupo asignado...');

        // Obtener alumnos activos sin grupo
        $alumnosSinGrupo = HistorialAcademico::whereNull('grupo_id')
            ->where('estatus', 'activo')
            ->with('alumno')
            ->get();

        if ($alumnosSinGrupo->isEmpty()) {
            $this->info('No hay alumnos pendientes de asignación de grupo.');
            return 0;
        }

        $this->info("Encontrados {$alumnosSinGrupo->count()} alumnos sin grupo.");

        $asignados = 0;
        $noAsignados = 0;

        foreach ($alumnosSinGrupo as $historial) {
            $alumno = $historial->alumno;
            
            // Determinar el grado del alumno (esto depende de tu lógica)
            // Por ahora, intentaremos con todos los grados disponibles
            $grado = $this->determinarGradoAlumno($historial);
            
            if (!$grado) {
                $this->warn("No se pudo determinar el grado para el alumno {$alumno->matricula}");
                $noAsignados++;
                continue;
            }

            // Buscar grupo disponible
            $grupo = $this->buscarGrupoDisponible($grado);

            if ($grupo) {
                $historial->update(['grupo_id' => $grupo->id]);
                $this->info("✓ Alumno {$alumno->matricula} asignado al grupo {$grupo->clave}");
                $asignados++;
            } else {
                $this->warn("✗ No hay grupos disponibles para el alumno {$alumno->matricula} (grado {$grado})");
                $noAsignados++;
            }
        }

        $this->info("\n=== Resumen ===");
        $this->info("Alumnos asignados: {$asignados}");
        $this->info("Alumnos sin asignar: {$noAsignados}");

        return 0;
    }

    private function determinarGradoAlumno(HistorialAcademico $historial): ?int
    {
        // Intentar determinar el grado basado en el ciclo escolar y la generación
        $alumno = $historial->alumno;
        $cicloActual = (int) date('Y');
        
        if ($alumno->generacion) {
            $aniosRestantes = $alumno->generacion - $cicloActual;
            
            return match($aniosRestantes) {
                3 => 1, // Primero
                2 => 2, // Segundo
                1 => 3, // Tercero
                default => 1, // Por defecto primero
            };
        }
        
        return 1; // Por defecto primero
    }

    private function buscarGrupoDisponible(int $grado): ?Grupo
    {
        $grupos = Grupo::where('grado', $grado)->get();
        
        if ($grupos->isEmpty()) {
            return null;
        }

        // Contar alumnos activos por grupo
        $gruposConConteos = $grupos->map(function ($grupo) {
            $totalAlumnos = HistorialAcademico::where('grupo_id', $grupo->id)
                ->where('estatus', 'activo')
                ->count();
            
            return [
                'grupo' => $grupo,
                'total' => $totalAlumnos,
                'disponibles' => 45 - $totalAlumnos,
            ];
        })->filter(function ($info) {
            return $info['disponibles'] > 0;
        });

        if ($gruposConConteos->isEmpty()) {
            return null;
        }

        // Asignar al grupo con menos alumnos
        $grupoSeleccionado = $gruposConConteos->sortBy('total')->first();
        
        return $grupoSeleccionado['grupo'];
    }
}
