<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Anuncio;
use App\Models\Alumno;
use App\Models\ContactoAlumno;
use App\Models\Salud;
use App\Models\Grupo;
use App\Models\HistorialAcademico;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    // El middleware ya está aplicado en las rutas (routes/admin.php)
    // No es necesario duplicarlo aquí

    public function dashboard(): Response
    {
        $user = auth()->user();

        // Obtener alertas no resueltas
        $alertas = \DB::table('alertas_sistema')
            ->where('resuelta', 0)
            ->orderByDesc('fecha_creacion')
            ->get();

        // Calcular estadísticas de asistencia del día actual
        $hoy = now()->format('Y-m-d');
        
        // Total de alumnos inscritos en el sistema (se usa en varias partes)
        $totalAlumnosInscritos = Alumno::where('estatus', 'activo')->count();
        
        // Verificar si hay registros de asistencia para hoy
        $hayRegistrosHoy = \DB::table('asistencias')
            ->where('fecha', $hoy)
            ->exists();
        
        $asistenciaHoy = null;
        
        if ($hayRegistrosHoy) {
            
            // Contar alumnos únicos con registro de asistencia presente
            $asistenciasHoy = \DB::table('asistencias')
                ->where('fecha', $hoy)
                ->where('estado', 'presente')
                ->distinct()
                ->count('alumno_id');
            
            // Contar total de alumnos que tienen registro (presente, falta, retardo)
            $alumnosConRegistro = \DB::table('asistencias')
                ->where('fecha', $hoy)
                ->distinct()
                ->count('alumno_id');
            
            // Los ausentes son los que tienen registro pero NO están presentes
            $ausentes = $alumnosConRegistro - $asistenciasHoy;
            
            $porcentajeAsistencia = $alumnosConRegistro > 0 ? ($asistenciasHoy / $alumnosConRegistro) * 100 : 0;
            $porcentajeAusencia = 100 - $porcentajeAsistencia;
            
            // Determinar nivel de alerta
            $nivelAlerta = 'normal';
            if ($porcentajeAusencia >= 30) {
                $nivelAlerta = 'critico';
            } elseif ($porcentajeAusencia >= 20) {
                $nivelAlerta = 'alto';
            } elseif ($porcentajeAusencia >= 15) {
                $nivelAlerta = 'medio';
            }

            // Estadísticas por grado
            $asistenciasPorGrado = \DB::table('asistencias')
                ->join('alumnos', 'asistencias.alumno_id', '=', 'alumnos.id')
                ->join('grupos', 'asistencias.grupo_id', '=', 'grupos.id')
                ->where('asistencias.fecha', $hoy)
                ->where('alumnos.estatus', 'activo')
                ->select(
                    'grupos.grado',
                    \DB::raw('COUNT(DISTINCT asistencias.alumno_id) as total'),
                    \DB::raw('SUM(CASE WHEN asistencias.estado = "presente" THEN 1 ELSE 0 END) as presentes')
                )
                ->groupBy('grupos.grado')
                ->orderBy('grupos.grado')
                ->get()
                ->map(function($row) {
                    $porcentaje = $row->total > 0 ? ($row->presentes / $row->total) * 100 : 0;
                    $ausencia = 100 - $porcentaje;
                    return [
                        'grado' => $row->grado . '°',
                        'total' => $row->total,
                        'presentes' => $row->presentes,
                        'ausentes' => $row->total - $row->presentes,
                        'porcentaje_asistencia' => round($porcentaje, 1),
                        'porcentaje_ausencia' => round($ausencia, 1),
                    ];
                });

            $asistenciaHoy = [
                'fecha' => $hoy,
                'total_alumnos' => $totalAlumnosInscritos,
                'presentes' => $asistenciasHoy,
                'ausentes' => $ausentes,
                'porcentaje_asistencia' => round($porcentajeAsistencia, 1),
                'porcentaje_ausencia' => round($porcentajeAusencia, 1),
                'nivel_alerta' => $nivelAlerta,
                'por_grado' => $asistenciasPorGrado,
            ];
        }

        // Estadísticas generales
        $stats = [
            'total_usuarios' => Usuario::count(),
            'total_alumnos' => $totalAlumnosInscritos,
            'docentes_activos' => Usuario::where('rol', 'Administrativo')->where('activo', 1)->count(),
            'grupos' => Grupo::count(),
            'total_anuncios' => Anuncio::where('activo', 1)->count(),
            'cuestionarios_activos' => \DB::table('cuestionarios_inscrip_reinscrip')->where('activo', 1)->count(),
            'alertas_pendientes' => $alertas->count(),
        ];

        $usuarios_recientes = Usuario::active()
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($usuario) {
                return [
                    'id' => $usuario->id,
                    'nombre_completo' => $usuario->nombre_completo,
                    'correo' => $usuario->correo,
                    'rol' => $usuario->rol,
                ];
            });

        $anuncios_recientes = Anuncio::orderBy('fecha', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($anuncio) {
                return [
                    'id' => $anuncio->id,
                    'titulo' => $anuncio->titulo,
                    'contenido' => $anuncio->contenido,
                    'fecha' => optional($anuncio->fecha)->format('d/m/Y'),
                    'activo' => (bool) $anuncio->activo,
                ];
            });

        return Inertia::render('Admin/Dashboard', [
            'user' => [
                'id' => $user->id,
                'nombre_completo' => $user->nombre_completo,
                'correo' => $user->correo,
                'rol' => $user->rol,
            ],
            'stats' => $stats,
            'usuarios_recientes' => $usuarios_recientes,
            'anuncios_recientes' => $anuncios_recientes,
            'alertas' => $alertas,
            'asistencia_hoy' => $asistenciaHoy,
        ]);
    }

    // Endpoints de datos simples (opcional)
    public function getUsuarios(Request $request)
    {
        $query = Usuario::active();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre_s', 'like', "%{$search}%")
                    ->orWhere('apellido_paterno', 'like', "%{$search}%")
                    ->orWhere('apellido_materno', 'like', "%{$search}%")
                    ->orWhere('correo', 'like', "%{$search}%");
            });
        }

        if ($request->has('rol')) {
            $query->where('rol', $request->get('rol'));
        }

        $usuarios = $query->orderBy('nombre_s')
            ->paginate($request->get('per_page', 15))
            ->through(function ($usuario) {
                return [
                    'id' => $usuario->id,
                    'nombre_completo' => $usuario->nombre_completo,
                    'correo' => $usuario->correo,
                    'rol' => $usuario->rol,
                    'activo' => $usuario->activo,
                ];
            });

        return response()->json($usuarios);
    }

    public function getAlumnos(Request $request)
    {
        // Si se agrega el modelo Alumno, aquí se implementa el filtro/paginación.
        return response()->json(['data' => [], 'total' => 0]);
    }

    public function perfilAlumno($id): Response
    {
        $alumno = Alumno::with(['salud', 'contactos', 'historialActual.grupo'])
            ->findOrFail($id);

        // Obtener datos del historial actual
        $historialActual = $alumno->historialActual;
        
        $grupos = Grupo::orderBy('grado')
            ->orderBy('clave')
            ->get()
            ->map(function ($grupo) {
                return [
                    'id' => $grupo->id,
                    'nombre' => "{$grupo->grado}° {$grupo->clave} - {$grupo->turno}",
                    'grado' => $grupo->grado,
                    'clave' => $grupo->clave,
                    'turno' => $grupo->turno,
                ];
            });

        return Inertia::render('Admin/Alumnos/Perfil', [
            'alumno' => [
                'id' => $alumno->id,
                'curp' => $alumno->curp,
                'matricula' => $alumno->matricula,
                'nombre_s' => $alumno->nombre_s,
                'apellido_paterno' => $alumno->apellido_paterno,
                'apellido_materno' => $alumno->apellido_materno,
                'nombre_completo' => $alumno->nombre_completo,
                'fecha_nacimiento' => $alumno->fecha_nacimiento ? $alumno->fecha_nacimiento->format('Y-m-d') : null,
                'sexo' => $alumno->sexo,
                // Datos del historial actual
                'generacion' => $alumno->generacion,
                'grado' => $historialActual && $historialActual->grupo ? $historialActual->grupo->grado : null,
                'grupo' => $historialActual && $historialActual->grupo ? $historialActual->grupo->clave : null,
                'grupo_id' => $historialActual->grupo_id ?? null,
                'turno' => $historialActual && $historialActual->grupo ? $historialActual->grupo->turno : null,
                'estatus' => ($historialActual ? $historialActual->estatus : null) ?? $alumno->estatus ?? 'activo',
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
                        'autorizado_recoger' => $contacto->autorizado_recoger,
                        'foto_url' => $contacto->foto_url,
                        'es_principal' => $contacto->es_principal ?? false,
                        'activo' => $contacto->activo ?? true,
                    ];
                }),
                'historial' => $alumno->historialAcademico->map(function ($historial) {
                    return [
                        'id' => $historial->id,
                        'ciclo' => $historial->ciclo,
                        'estatus' => $historial->estatus,
                        'grupo' => $historial->grupo ? [
                            'grado' => $historial->grupo->grado,
                            'clave' => $historial->grupo->clave,
                            'turno' => $historial->grupo->turno,
                        ] : null,
                    ];
                }),
            ],
            'grupos' => $grupos,
        ]);
    }

    public function actualizarAlumno(Request $request, $id)
    {
        $alumno = Alumno::findOrFail($id);

        $validated = $request->validate([
            'nombre_s' => 'required|string|max:80',
            'apellido_paterno' => 'required|string|max:50',
            'apellido_materno' => 'required|string|max:50',
            'fecha_nacimiento' => 'required|date',
            'sexo' => 'required|in:M,F,X',
            'generacion' => 'required|string|max:40',
            'grupo_id' => 'nullable|exists:grupos,id',
            'estatus' => 'required|in:inscrito,activo,baja,egresado',
            'salud.tipo_sangre' => 'nullable|string|max:5',
            'salud.alergias' => 'nullable|string',
            'salud.enfermedades_cronicas' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,jpg,png|max:5120', // Máximo 5MB
        ]);

        // Manejar la foto si se subió una nueva
        if ($request->hasFile('foto')) {
            // Eliminar foto anterior si existe
            if ($alumno->archivo_multimedia_id) {
                $archivoAnterior = \App\Models\ArchivoMultimedia::find($alumno->archivo_multimedia_id);
                if ($archivoAnterior) {
                    $archivoAnterior->delete();
                }
            }

            // Guardar nueva foto en la base de datos
            $file = $request->file('foto');
            $nombreArchivo = $alumno->matricula . '.' . $file->getClientOriginalExtension();
            
            $archivoMultimedia = \App\Models\ArchivoMultimedia::create([
                'nombre_archivo' => $nombreArchivo,
                'tipo_mime' => $file->getMimeType(),
                'tipo_archivo' => 'foto_alumno',
                'tamano' => $file->getSize(),
                'contenido' => file_get_contents($file->getRealPath()),
            ]);
            
            $alumno->archivo_multimedia_id = $archivoMultimedia->id;
            $alumno->save();
        }

        // Actualizar datos del alumno
        $alumno->update([
            'nombre_s' => $validated['nombre_s'],
            'apellido_paterno' => $validated['apellido_paterno'],
            'apellido_materno' => $validated['apellido_materno'],
            'fecha_nacimiento' => $validated['fecha_nacimiento'],
            'sexo' => $validated['sexo'],
            'generacion' => $validated['generacion'],
            'estatus' => $validated['estatus'],
        ]);

        // Actualizar o crear historial académico actual
        if (isset($validated['grupo_id'])) {
            $cicloActual = date('Y') . '-' . (date('Y') + 1);
            
            // Mapear estatus de alumno a estatus de historial
            $estatusHistorial = match($validated['estatus']) {
                'activo' => 'inscrito',
                'baja' => 'baja',
                'egresado' => 'egresado',
                default => 'inscrito'
            };
            
            // Primero eliminar cualquier registro duplicado del ciclo actual
            HistorialAcademico::where('alumno_id', $alumno->id)
                ->where('ciclo', $cicloActual)
                ->delete();
            
            // Luego crear el nuevo registro
            HistorialAcademico::create([
                'alumno_id' => $alumno->id,
                'ciclo' => $cicloActual,
                'grupo_id' => $validated['grupo_id'],
                'estatus' => $estatusHistorial,
            ]);
        }

        // Actualizar o crear información de salud
        if (isset($validated['salud'])) {
            Salud::updateOrCreate(
                ['alumno_id' => $alumno->id],
                [
                    'tipo_sangre' => $validated['salud']['tipo_sangre'] ?? null,
                    'alergias' => $validated['salud']['alergias'] ?? null,
                    'enfermedades_cronicas' => $validated['salud']['enfermedades_cronicas'] ?? null,
                ]
            );
        }

        // Limpiar cualquier relación cargada que pueda causar problemas
        $alumno->unsetRelations();

        return redirect()->back()->with('success', 'Datos del alumno actualizados correctamente');
    }

    // ==================== GESTIÓN DE GRUPOS ====================

    public function grupos(Request $request): Response
    {
        $grado = $request->get('grado');
        $clave = $request->get('clave');
        $turno = $request->get('turno');

        \Log::info('Consultando grupos', ['grado' => $grado, 'clave' => $clave, 'turno' => $turno]);

        // Contar alumnos activos sin filtrar por ciclo
        $query = Grupo::query()->withCount([
            'historialAcademico as alumnos_count'
        ]);

        if ($grado) {
            $query->where('grado', $grado);
        }

        if ($clave) {
            $query->where('clave', $clave);
        }

        if ($turno) {
            $query->where('turno', $turno);
        }

        $grupos = $query->orderBy('grado')
            ->orderBy('clave')
            ->orderBy('turno')
            ->get()
            ->map(function ($grupo) {
                return [
                    'id' => $grupo->id,
                    'grado' => $grupo->grado,
                    'clave' => $grupo->clave,
                    'turno' => $grupo->turno,
                    'nombre' => "{$grupo->grado}° {$grupo->clave} - {$grupo->turno}",
                    'alumnos_count' => $grupo->alumnos_count,
                ];
            });

        \Log::info('Grupos encontrados', ['total' => $grupos->count(), 'grupos' => $grupos->toArray()]);

        return Inertia::render('Admin/Alumnos/Grupos', [
            'grupos' => $grupos,
            'filtros' => [
                'grado' => $grado,
                'clave' => $clave,
                'turno' => $turno,
            ],
        ]);
    }

    public function crearGrupo(Request $request)
    {
        $validated = $request->validate([
            'grado' => 'required|integer|min:1|max:3',
            'clave' => 'required|string|max:10',
            'turno' => 'required|in:Matutino,Vespertino',
        ]);

        // Verificar que no exista el mismo grupo
        $existe = Grupo::where('grado', $validated['grado'])
            ->where('clave', $validated['clave'])
            ->where('turno', $validated['turno'])
            ->exists();

        if ($existe) {
            return redirect()->back()->withErrors(['error' => 'Este grupo ya existe']);
        }

        Grupo::create($validated);

        // Disparar trabajo en segundo plano para asignar alumnos sin grupo
        // TODO: Corregir el Job AsignarGruposAutomaticamente antes de habilitarlo
        // \App\Jobs\AsignarGruposAutomaticamente::dispatch();

        return redirect()->back()->with('success', 'Grupo creado correctamente');
    }

    public function actualizarGrupo(Request $request, $id)
    {
        $grupo = Grupo::findOrFail($id);

        $validated = $request->validate([
            'grado' => 'required|integer|min:1|max:3',
            'clave' => 'required|string|max:10',
            'turno' => 'required|in:Matutino,Vespertino',
        ]);

        // Verificar que no exista otro grupo con los mismos datos
        $existe = Grupo::where('grado', $validated['grado'])
            ->where('clave', $validated['clave'])
            ->where('turno', $validated['turno'])
            ->where('id', '!=', $id)
            ->exists();

        if ($existe) {
            return redirect()->back()->withErrors(['error' => 'Ya existe otro grupo con estos datos']);
        }

        $grupo->update($validated);

        return redirect()->back()->with('success', 'Grupo actualizado correctamente');
    }

    public function eliminarGrupo(Request $request, $id)
    {
        // Obtener la contraseña del request
        $password = $request->input('password');
        
        if (!$password) {
            return back()->withErrors(['error' => 'Debe proporcionar su contraseña']);
        }

        $user = \Auth::user();
        
        if (!$user) {
            \Log::error('Usuario no encontrado al intentar eliminar grupo');
            return back()->withErrors(['error' => 'Sesión inválida. Por favor, inicia sesión nuevamente.']);
        }
        
        // Verificar contraseña (usar hash_contrasena que es el campo real en la tabla)
        if (!\Hash::check($password, $user->hash_contrasena)) {
            return back()->withErrors(['error' => 'Contraseña incorrecta']);
        }

        $grupo = Grupo::findOrFail($id);

        // Verificar si tiene alumnos
        $tieneAlumnos = $grupo->historialAcademico()->exists();

        if ($tieneAlumnos) {
            return back()->withErrors(['error' => 'No se puede eliminar un grupo que tiene alumnos asignados']);
        }

        $grupo->delete();

        return back()->with('success', 'Grupo eliminado correctamente');
    }

    public function alumnosGrupo($id): Response
    {
        $grupo = Grupo::findOrFail($id);

        $alumnos = Alumno::whereHas('historialActual', function ($query) use ($id) {
            $query->where('grupo_id', $id);
        })
        ->with(['historialActual', 'salud'])
        ->orderBy('apellido_paterno')
        ->orderBy('apellido_materno')
        ->orderBy('nombre_s')
        ->get()
        ->map(function ($alumno) {
            return [
                'id' => $alumno->id,
                'matricula' => $alumno->matricula,
                'nombre_completo' => $alumno->nombre_completo,
                'foto_url' => $alumno->foto_url,
                'estatus' => $alumno->historialActual->estatus ?? 'activo',
            ];
        });

        return Inertia::render('Admin/Alumnos/GrupoDetalle', [
            'grupo' => [
                'id' => $grupo->id,
                'grado' => $grupo->grado,
                'clave' => $grupo->clave,
                'turno' => $grupo->turno,
                'nombre' => "{$grupo->grado}° {$grupo->clave} - {$grupo->turno}",
            ],
            'alumnos' => $alumnos,
        ]);
    }

    public function contactos(): Response
    {
        return Inertia::render('Admin/Alumnos/Contacto');
    }

    public function listarContactos()
    {
        $contactos = ContactoAlumno::with(['alumno.historialActual.grupo', 'archivoMultimedia'])
            ->orderBy('apellido_paterno', 'asc')
            ->orderBy('apellido_materno', 'asc')
            ->orderBy('nombre_s', 'asc')
            ->get()
            ->map(function($contacto) {
                return [
                    'id' => $contacto->id,
                    'nombre_completo' => $contacto->nombre_completo,
                    'parentesco' => $contacto->parentesco ?? 'No especificado',
                    'foto_url' => $contacto->foto_url,
                    'alumno_nombre' => $contacto->alumno->nombre_completo,
                    'alumno_matricula' => $contacto->alumno->matricula,
                    'alumno_id' => $contacto->alumno->id,
                    'alumno_grado' => $contacto->alumno->historialActual?->grupo?->grado,
                    'alumno_grupo' => $contacto->alumno->historialActual?->grupo?->clave,
                ];
            });

        return response()->json($contactos);
    }

    public function resolverAlerta(Request $request, $id)
    {
        $validated = $request->validate([
            'nota_resolucion' => 'nullable|string',
        ]);

        \DB::table('alertas_sistema')
            ->where('id', $id)
            ->update([
                'resuelta' => 1,
                'fecha_resolucion' => now(),
                'nota_resolucion' => $validated['nota_resolucion'] ?? 'Resuelta manualmente por el administrador',
            ]);

        return redirect()->back()->with('success', 'Alerta resuelta correctamente');
    }
}

