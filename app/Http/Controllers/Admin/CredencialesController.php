<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\ContactoAlumno;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class CredencialesController extends Controller
{
    public function generar(Request $request)
    {
        $tipo = $request->input('tipo', 'individual');

        if ($tipo === 'individual') {
            return $this->generarIndividual($request->input('matricula'));
        } else {
            return $this->generarMasivo();
        }
    }

    private function generarIndividual($matricula)
    {
        $alumno = Alumno::with(['salud', 'contactoPrincipal', 'historialActual.grupo'])
            ->where('matricula', $matricula)
            ->firstOrFail();
        
        $historialActual = $alumno->historialActual;
        $grado = $historialActual && $historialActual->grupo ? $historialActual->grupo->grado : $alumno->grado;
        $grupo = $historialActual && $historialActual->grupo ? $historialActual->grupo->clave : $alumno->grupo;
        
        // Validar que el alumno tenga grado y grupo
        if (!$grado || !$grupo) {
            return response()->json([
                'error' => 'El alumno no tiene grado o grupo asignado. Por favor, actualice la información del alumno.'
            ], 422);
        }
        
        $contacto = $alumno->contactoPrincipal;

        $datos = $this->prepararDatosCredencial($alumno, $contacto);

        $pdf = Pdf::loadView('credenciales.template', ['alumno' => $datos]);
        $pdf->setPaper([0, 0, 252, 396], 'portrait'); // Tamaño credencial: 8.9cm x 14cm

        $response = $pdf->download("credencial_{$matricula}.pdf");
        
        // Limpiar archivo temporal si se creó
        if (isset($datos['foto_path']) && strpos($datos['foto_path'], sys_get_temp_dir()) !== false) {
            @unlink($datos['foto_path']);
        }
        
        return $response;
    }

    private function generarMasivo()
    {
        $alumnos = Alumno::with(['salud', 'contactoPrincipal', 'historialActual.grupo'])
            ->where('estatus', 'activo')
            ->get()
            ->filter(function ($alumno) {
                // Filtrar alumnos que tengan grado y grupo (ya sea en historial o en columnas directas)
                $historialActual = $alumno->historialActual;
                $grado = $historialActual && $historialActual->grupo ? $historialActual->grupo->grado : $alumno->grado;
                $grupo = $historialActual && $historialActual->grupo ? $historialActual->grupo->clave : $alumno->grupo;
                return $grado && $grupo;
            });
        
        if ($alumnos->isEmpty()) {
            return response()->json([
                'error' => 'No hay alumnos con grado y grupo asignados para generar credenciales.'
            ], 422);
        }
        
        $zipFileName = 'credenciales_' . date('Y-m-d_His') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);
        
        // Crear directorio temporal si no existe
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new ZipArchive();
        
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $archivosTemporales = [];
            
            foreach ($alumnos as $alumno) {
                $contacto = $alumno->contactoPrincipal;

                $datos = $this->prepararDatosCredencial($alumno, $contacto);
                
                // Guardar archivos temporales para limpiarlos después
                if (isset($datos['foto_path']) && strpos($datos['foto_path'], sys_get_temp_dir()) !== false) {
                    $archivosTemporales[] = $datos['foto_path'];
                }

                $pdf = Pdf::loadView('credenciales.template', ['alumno' => $datos]);
                $pdf->setPaper([0, 0, 252, 396], 'portrait');

                $pdfContent = $pdf->output();
                $zip->addFromString("credencial_{$alumno->matricula}.pdf", $pdfContent);
            }
            
            $zip->close();
            
            // Limpiar archivos temporales
            foreach ($archivosTemporales as $tempFile) {
                @unlink($tempFile);
            }

            return response()->download($zipPath)->deleteFileAfterSend(true);
        }

        return response()->json(['error' => 'No se pudo crear el archivo ZIP'], 500);
    }

    private function prepararDatosCredencial($alumno, $contacto = null)
    {
        // Obtener la ruta de la foto del alumno para DomPDF
        $fotoPath = public_path('images/avatar-default.svg');
        
        // Si la foto está en archivos_multimedia, crear archivo temporal
        if ($alumno->archivoMultimedia) {
            $tempPath = tempnam(sys_get_temp_dir(), 'foto_');
            file_put_contents($tempPath, $alumno->archivoMultimedia->contenido);
            $fotoPath = $tempPath;
        }

        // Obtener datos de salud
        $salud = $alumno->salud;
        $tipo_sangre = $salud ? $salud->tipo_sangre : 'N/A';
        $alergias = $salud ? $salud->alergias : 'Ninguna';
        
        // Obtener grado y grupo del historial académico actual o fallback
        $historialActual = $alumno->historialActual;
        $grado = $historialActual && $historialActual->grupo ? $historialActual->grupo->grado : $alumno->grado;
        $grupo = $historialActual && $historialActual->grupo ? $historialActual->grupo->clave : $alumno->grupo;

        return [
            'nombre_completo' => $alumno->nombre_completo,
            'matricula' => $alumno->matricula,
            'grado' => $grado,
            'grupo' => $grupo,
            'tipo_sangre' => $tipo_sangre ?? 'N/A',
            'alergias' => $alergias ?? 'Ninguna',
            'foto_path' => $fotoPath,
            'telefono_tutor' => $contacto ? $contacto->telefono : 'N/A',
            'escuela' => 'ESCUELA SECUNDARIA TECNICA 40',
            'escuela_completa' => '"LUIS DONALDO COLOSIO MURRIETA"',
        ];
    }

    public function listarAlumnos()
    {
        $alumnos = Alumno::with(['salud', 'contactoPrincipal', 'historialActual.grupo'])
            ->where('estatus', 'activo')
            ->orderBy('apellido_paterno', 'asc')
            ->orderBy('apellido_materno', 'asc')
            ->orderBy('nombre_s', 'asc')
            ->get()
            ->map(function ($alumno) {
                $contacto = $alumno->contactoPrincipal;
                $salud = $alumno->salud;
                $historialActual = $alumno->historialActual;
                
                return [
                    'id' => $alumno->id,
                    'matricula' => $alumno->matricula,
                    'nombre_completo' => $alumno->nombre_completo,
                    'curp' => $alumno->curp,
                    'grado' => $historialActual && $historialActual->grupo ? $historialActual->grupo->grado : null,
                    'grupo' => $historialActual && $historialActual->grupo ? $historialActual->grupo->clave : null,
                    'turno' => $historialActual && $historialActual->grupo ? $historialActual->grupo->turno : null,
                    'estatus' => $historialActual->estatus ?? $alumno->estatus,
                    'tipo_sangre' => $salud ? $salud->tipo_sangre : null,
                    'foto_url' => $alumno->foto_url,
                    'alergias' => $salud ? $salud->alergias : null,
                    'telefono_tutor' => $contacto ? $contacto->telefono : null,
                ];
            });

        return response()->json($alumnos);
    }
}
