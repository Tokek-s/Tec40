<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Salud;
use App\Models\ContactoAlumno;
use App\Models\ArchivoMultimedia;
use App\Models\FechaCuestionario;
use App\Models\Grupo;
use App\Models\HistorialAcademico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class FormularioInscripcionController extends Controller
{
    public function mostrar(Request $request, $tipo): Response
    {
        // $tipo puede ser: 'primero', 'segundo', 'tercero', 'reinscripcion'
        $grado = $this->obtenerGrado($tipo);
        $esReinscripcion = ($tipo === 'reinscripcion');

        // Obtener cuestionario activo
        $cuestionario = FechaCuestionario::where('activo', 1)
            ->whereDate('fecha_inicio', '<=', now())
            ->whereDate('fecha_fin', '>=', now())
            ->when(!$esReinscripcion, function ($query) use ($tipo) {
                $campoActivo = $tipo . '_activo';
                $query->where($campoActivo, 1);
            })
            ->first();

        if (!$cuestionario) {
            return Inertia::render('FormularioInscripcion/NoDisponible', [
                'mensaje' => 'No hay inscripciones disponibles en este momento.',
            ]);
        }

        // Obtener campos extra dinámicos
        $camposExtra = $this->obtenerCamposExtra();

        return Inertia::render('FormularioInscripcion/Formulario', [
            'tipo' => $tipo,
            'grado' => $grado,
            'esReinscripcion' => $esReinscripcion,
            'cuestionario' => $cuestionario,
            'camposExtra' => $camposExtra,
        ]);
    }

    public function guardar(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'required|string|in:primero,segundo,tercero,reinscripcion',
            
            // Datos del alumno
            'curp' => 'required|string|size:18',
            'nombre' => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'apellido_materno' => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date',
            'sexo' => 'required|string|in:M,F',
            
            // Datos de salud
            'tipo_sangre' => 'required|string',
            'alergias' => 'nullable|string',
            'enfermedades' => 'nullable|string',
            'medicamentos' => 'nullable|string',
            
            // Contactos (2-3)
            'contactos' => 'required|array|min:1|max:3',
            'contactos.*.nombre' => 'required|string|max:255',
            'contactos.*.apellido_paterno' => 'required|string|max:255',
            'contactos.*.apellido_materno' => 'required|string|max:255',
            'contactos.*.telefono' => 'required|string|max:20',
            'contactos.*.correo' => 'nullable|email',
            'contactos.*.parentesco' => 'required|string',
            'contactos.*.autorizado_recoger' => 'required|integer|in:0,1',
            
            // Foto
            'foto' => 'required|string', // Base64
            
            // Foto del alumno
            'foto_alumno' => 'nullable|file|image|max:2048', // 2MB
            
            // Fotos de contactos
            'foto_contacto_0' => 'nullable|file|image|max:2048',
            'foto_contacto_1' => 'nullable|file|image|max:2048',
            'foto_contacto_2' => 'nullable|file|image|max:2048',
            
            // Campos extra dinámicos
            'campos_extra' => 'nullable|array',
        ]);

        // Verificar si la CURP ya está registrada
        $alumnoExistente = Alumno::where('curp', $validated['curp'])->first();
        
        if ($alumnoExistente) {
            return $this->mostrarResumenAlumnoExistente($alumnoExistente);
        }

        try {
            DB::beginTransaction();

            // 1. Generar matrícula única
            $matricula = $this->generarMatriculaUnica();

            // 2. Determinar el grado
            $grado = $this->obtenerGrado($validated['tipo']);
            
            // 3. Calcular la generación
            $generacion = $this->calcularGeneracion($grado);

            // 4. Determinar turno automático (primer turno disponible)
            $turno = $this->determinarTurno();

            // 5. Crear registro del alumno
            $alumno = Alumno::create([
                'curp' => $validated['curp'],
                'nombre_s' => $validated['nombre'],
                'apellido_paterno' => $validated['apellido_paterno'],
                'apellido_materno' => $validated['apellido_materno'],
                'fecha_nacimiento' => $validated['fecha_nacimiento'],
                'sexo' => $validated['sexo'],
                'matricula' => $matricula,
                'generacion' => $generacion,
                'estatus' => 'activo',
            ]);

            // 6. Guardar foto (base64 existente)
            $fotoBase64 = $this->guardarFoto($validated['foto'], $alumno->id);
            if ($fotoBase64) {
                $alumno->archivo_multimedia_id = $fotoBase64->id;
                $alumno->save();
            }

            // 6b. Guardar foto del alumno (nueva funcionalidad - sobrescribe la anterior si existe)
            if ($request->hasFile('foto_alumno')) {
                $fotoAlumno = $this->guardarFotoAlumno($request->file('foto_alumno'), $alumno->id);
                if ($fotoAlumno) {
                    $alumno->archivo_multimedia_id = $fotoAlumno->id;
                    $alumno->save();
                }
            }

            // 7. Guardar datos de salud
            Salud::create([
                'alumno_id' => $alumno->id,
                'tipo_sangre' => $validated['tipo_sangre'],
                'alergias' => $validated['alergias'],
                'enfermedades' => $validated['enfermedades'],
                'medicamentos' => $validated['medicamentos'],
            ]);

            // 8. Guardar contactos
            foreach ($validated['contactos'] as $index => $contacto) {
                $contactoCreado = ContactoAlumno::create([
                    'alumno_id' => $alumno->id,
                    'nombre_s' => $contacto['nombre'],
                    'apellido_paterno' => $contacto['apellido_paterno'],
                    'apellido_materno' => $contacto['apellido_materno'],
                    'telefono' => $contacto['telefono'],
                    'correo' => $contacto['correo'] ?? null,
                    'parentesco' => $contacto['parentesco'],
                    'autorizado_recoger' => $contacto['autorizado_recoger'],
                ]);

                // Guardar foto del contacto si existe
                $fotoKey = "foto_contacto_{$index}";
                if ($request->hasFile($fotoKey)) {
                    $fotoContacto = $this->guardarFotoContacto($request->file($fotoKey), $contactoCreado->id, $alumno->id);
                    if ($fotoContacto) {
                        $contactoCreado->archivo_multimedia_id = $fotoContacto->id;
                        $contactoCreado->save();
                    }
                }
            }

            // 9. Guardar campos extra en datos_extra
            $this->guardarCamposExtra($alumno->id, $validated['campos_extra']);

            // 10. Asignar grupo automáticamente
            $grupo = $this->asignarGrupoAutomatico($alumno, $grado);

            // 11. Crear registro en historial académico
            HistorialAcademico::create([
                'alumno_id' => $alumno->id,
                'grupo_id' => $grupo ? $grupo->id : null,
                'ciclo' => $this->obtenerCicloEscolarActual(),
                'estatus' => 'activo',
            ]);

            DB::commit();

            // Mensaje según si se asignó grupo o no
            if ($grupo) {
                return redirect()->route('home')->with('success', 'Inscripción realizada correctamente. Matrícula: ' . $matricula . ' - Grupo: ' . $grupo->clave);
            } else {
                return redirect()->route('home')->with('warning', 'Inscripción realizada correctamente. Matrícula: ' . $matricula . ' - El alumno será asignado a un grupo cuando haya disponibilidad.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error en inscripción: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la inscripción: ' . mb_convert_encoding($e->getMessage(), 'UTF-8', 'UTF-8'),
            ], 500);
        }
    }

    private function mostrarResumenAlumnoExistente(Alumno $alumno)
    {
        $alumno->load(['salud', 'contactos']);
        
        return response()->json([
            'success' => false,
            'alumno_existente' => true,
            'message' => 'El alumno con esta CURP ya está inscrito.',
            'data' => [
                'curp' => $alumno->curp,
                'nombre_completo' => "{$alumno->nombre} {$alumno->apellido_paterno} {$alumno->apellido_materno}",
                'matricula' => $alumno->matricula,
                'fecha_nacimiento' => $alumno->fecha_nacimiento,
                'salud' => $alumno->salud,
                'contactos' => $alumno->contactos,
            ],
            'nota' => 'Si está inconforme con algún dato, contacte a la escuela o acuda personalmente.',
        ], 409);
    }

    private function obtenerGrado(string $tipo): int
    {
        return match($tipo) {
            'primero' => 1,
            'segundo' => 2,
            'tercero' => 3,
            'reinscripcion' => 1, // Por defecto, se puede ajustar
            default => 1,
        };
    }

    private function generarMatriculaUnica(): string
    {
        $anio = date('Y');
        
        do {
            // Formato: YYYY40XXXXX (ej: 202440001)
            $ultimaMatricula = Alumno::where('matricula', 'like', "{$anio}40%")
                ->orderByDesc('matricula')
                ->first();
            
            if ($ultimaMatricula) {
                $ultimoNumero = (int) substr($ultimaMatricula->matricula, -5);
                $nuevoNumero = $ultimoNumero + 1;
            } else {
                $nuevoNumero = 1;
            }
            
            $matricula = $anio . '40' . str_pad($nuevoNumero, 5, '0', STR_PAD_LEFT);
            
            // Verificar que sea única
            $existe = Alumno::where('matricula', $matricula)->exists();
            
        } while ($existe);
        
        return $matricula;
    }

    private function calcularGeneracion(int $grado): int
    {
        $anioActual = (int) date('Y');
        
        return match($grado) {
            1 => $anioActual + 3,
            2 => $anioActual + 2,
            3 => $anioActual + 1,
            default => $anioActual + 3,
        };
    }

    private function determinarTurno(): string
    {
        // Por defecto matutino, puedes implementar lógica más compleja
        return 'matutino';
    }

    private function guardarFoto(string $fotoBase64, int $alumnoId): ?ArchivoMultimedia
    {
        // Remover prefijo data:image/...;base64,
        $fotoBase64 = preg_replace('/^data:image\/\w+;base64,/', '', $fotoBase64);
        $fotoData = base64_decode($fotoBase64);
        
        $nombreArchivo = "foto_alumno_{$alumnoId}_" . time() . ".jpg";
        
        return ArchivoMultimedia::create([
            'nombre_archivo' => $nombreArchivo,
            'tipo_archivo' => 'image/jpeg',
            'tipo_mime' => 'image/jpeg',
            'tamano' => strlen($fotoData),
            'contenido' => $fotoData,
        ]);
    }

    private function guardarFotoAlumno($foto, int $alumnoId): ?ArchivoMultimedia
    {
        $nombreArchivo = "foto_alumno_{$alumnoId}_" . time() . "." . $foto->getClientOriginalExtension();
        
        return ArchivoMultimedia::create([
            'nombre_archivo' => $nombreArchivo,
            'tipo_archivo' => $foto->getMimeType(),
            'tipo_mime' => $foto->getMimeType(),
            'tamano' => $foto->getSize(),
            'contenido' => file_get_contents($foto),
        ]);
    }

    private function guardarFotoContacto($foto, int $contactoId, int $alumnoId): ?ArchivoMultimedia
    {
        $nombreArchivo = "foto_contacto_{$contactoId}_alumno_{$alumnoId}_" . time() . "." . $foto->getClientOriginalExtension();
        
        return ArchivoMultimedia::create([
            'nombre_archivo' => $nombreArchivo,
            'tipo_archivo' => $foto->getMimeType(),
            'tipo_mime' => $foto->getMimeType(),
            'tamano' => $foto->getSize(),
            'contenido' => file_get_contents($foto),
        ]);
    }

    private function guardarCamposExtra(int $alumnoId, array $campos): void
    {
        if (empty($campos)) {
            return;
        }

        // Verificar si ya existe un registro para este alumno
        $existe = DB::table('datos_extra')->where('alumno_id', $alumnoId)->exists();
        
        $datos = ['alumno_id' => $alumnoId];
        
        foreach ($campos as $nombre => $valor) {
            $nombreCampo = str_replace(' ', '_', strtolower($nombre));
            $nombreCampo = preg_replace('/[^a-z0-9_]/', '', $nombreCampo);
            $datos[$nombreCampo] = $valor;
        }
        
        if ($existe) {
            DB::table('datos_extra')->where('alumno_id', $alumnoId)->update($datos);
        } else {
            DB::table('datos_extra')->insert($datos);
        }
    }

    private function asignarGrupoAutomatico(Alumno $alumno, int $grado): ?Grupo
    {
        // Obtener todos los grupos disponibles para este grado
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
            return $info['disponibles'] > 0; // Solo grupos con espacio
        });

        if ($gruposConConteos->isEmpty()) {
            // Todos los grupos están llenos
            return null;
        }

        // Asignar al grupo con menos alumnos
        $grupoSeleccionado = $gruposConConteos->sortBy('total')->first();

        return $grupoSeleccionado['grupo'];
    }

    private function obtenerCamposExtra(): array
    {
        try {
            $columns = DB::select("SHOW COLUMNS FROM datos_extra");
            $camposExtra = [];
            
            foreach ($columns as $column) {
                if (!in_array($column->Field, ['id', 'alumno_id', 'created_at', 'updated_at'])) {
                    $camposExtra[] = [
                        'nombre' => $column->Field,
                        'label' => ucfirst(str_replace('_', ' ', $column->Field)),
                        'tipo' => 'texto',
                    ];
                }
            }
            
            return $camposExtra;
        } catch (\Exception $e) {
            return [];
        }
    }

    private function obtenerCicloEscolarActual(): string
    {
        $anioActual = (int) date('Y');
        $mesActual = (int) date('m');
        
        // Si estamos entre enero y julio, el ciclo es del año anterior
        if ($mesActual < 8) {
            return ($anioActual - 1) . '-' . $anioActual;
        }
        
        return $anioActual . '-' . ($anioActual + 1);
    }
}
