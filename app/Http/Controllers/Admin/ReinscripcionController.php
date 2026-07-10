<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\FechaCuestionario;
use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReinscripcionController extends Controller
{
    /**
     * Mostrar lista de cuestionarios de reinscripción
     */
    public function index()
    {
        $cuestionarios = FechaCuestionario::where('tipo', 'reinscripcion')
            ->orderBy('fecha_inicio', 'desc')
            ->get()
            ->map(function ($cuestionario) {
                // Obtener estadísticas de alumnos reinscritos por grado
                $reinscritos = DB::table('alumnos')
                    ->join('historiales_academicos', 'alumnos.id', '=', 'historiales_academicos.alumno_id')
                    ->join('grupos', 'historiales_academicos.grupo_id', '=', 'grupos.id')
                    ->where('historiales_academicos.activo', 1)
                    ->where('alumnos.estatus', 'activo')
                    ->select('grupos.grado', DB::raw('COUNT(*) as total'))
                    ->groupBy('grupos.grado')
                    ->get()
                    ->keyBy('grado');

                return [
                    'id' => $cuestionario->id,
                    'titulo' => $cuestionario->titulo,
                    'descripcion' => $cuestionario->descripcion,
                    'fecha_inicio' => $cuestionario->fecha_inicio->format('Y-m-d'),
                    'fecha_fin' => $cuestionario->fecha_fin->format('Y-m-d'),
                    'activo' => $cuestionario->activo,
                    'primero_activo' => $cuestionario->primero_activo,
                    'segundo_activo' => $cuestionario->segundo_activo,
                    'tercero_activo' => $cuestionario->tercero_activo,
                    'reinscritos_primero' => $reinscritos->get('1')->total ?? 0,
                    'reinscritos_segundo' => $reinscritos->get('2')->total ?? 0,
                    'reinscritos_tercero' => $reinscritos->get('3')->total ?? 0,
                ];
            });

        return Inertia::render('Admin/Inscripciones/Reinscripcion', [
            'cuestionarios' => $cuestionarios,
        ]);
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return Inertia::render('Admin/Inscripciones/CrearEditarReinscripcion', [
            'camposExtra' => [],
        ]);
    }

    /**
     * Guardar nuevo cuestionario de reinscripción
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'activo' => 'required|boolean',
            'primero_activo' => 'required|boolean',
            'segundo_activo' => 'required|boolean',
            'tercero_activo' => 'required|boolean',
            'campos_extra' => 'nullable|array',
            'campos_extra.*.nombre' => 'required|string',
            'campos_extra.*.tipo' => 'required|string|in:texto',
        ]);

        $error = $this->validarGruposDisponibles($validated);
        if ($error) {
            return back()->withErrors(['error' => $error])->withInput();
        }

        // Crear cuestionario
        $cuestionario = FechaCuestionario::create([
            'titulo' => 'Reinscripción ' . date('Y'),
            'descripcion' => $validated['descripcion'],
            'fecha_inicio' => $validated['fecha_inicio'],
            'fecha_fin' => $validated['fecha_fin'],
            'activo' => $validated['activo'],
            'primero_activo' => $validated['primero_activo'],
            'segundo_activo' => $validated['segundo_activo'],
            'tercero_activo' => $validated['tercero_activo'],
            'tipo' => 'reinscripcion',
        ]);

        return redirect()->route('admin.inscripciones.reinscripcion');
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $cuestionario = FechaCuestionario::findOrFail($id);

        return Inertia::render('Admin/Inscripciones/CrearEditarReinscripcion', [
            'cuestionario' => [
                'id' => $cuestionario->id,
                'titulo' => $cuestionario->titulo,
                'descripcion' => $cuestionario->descripcion,
                'fecha_inicio' => $cuestionario->fecha_inicio->format('Y-m-d'),
                'fecha_fin' => $cuestionario->fecha_fin->format('Y-m-d'),
                'activo' => $cuestionario->activo,
                'primero_activo' => $cuestionario->primero_activo,
                'segundo_activo' => $cuestionario->segundo_activo,
                'tercero_activo' => $cuestionario->tercero_activo,
            ],
            'camposExtra' => [],
        ]);
    }

    /**
     * Actualizar cuestionario
     */
    public function update(Request $request, $id)
    {
        $cuestionario = FechaCuestionario::findOrFail($id);

        $validated = $request->validate([
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'activo' => 'required|boolean',
            'primero_activo' => 'required|boolean',
            'segundo_activo' => 'required|boolean',
            'tercero_activo' => 'required|boolean',
        ]);

        $error = $this->validarGruposDisponibles($validated);
        if ($error) {
            return back()->withErrors(['error' => $error])->withInput();
        }

        $cuestionario->update($validated);

        return redirect()->route('admin.inscripciones.reinscripcion');
    }

    /**
     * Eliminar cuestionario
     */
    public function destroy($id)
    {
        $cuestionario = FechaCuestionario::findOrFail($id);
        $cuestionario->delete();

        return redirect()->route('admin.inscripciones.reinscripcion');
    }

    /**
     * Cerrar reinscripciones - Actualizar estatus de alumnos
     */
    public function cerrarReinscripciones(Request $request, $id)
    {
        $cuestionario = FechaCuestionario::findOrFail($id);

        // Validar contraseña
        $request->validate([
            'password' => 'required',
        ]);

        $usuario = auth()->user();
        if (!\Hash::check($request->password, $usuario->hash_contrasena)) {
            return back()->withErrors(['error' => 'Contraseña incorrecta']);
        }

        DB::beginTransaction();
        try {
            // Obtener todos los alumnos activos con su grado actual
            $alumnos = Alumno::with(['historialActual.grupo'])
                ->where('estatus', 'activo')
                ->get();

            foreach ($alumnos as $alumno) {
                $gradoActual = $alumno->historialActual?->grupo?->grado;

                if (!$gradoActual) {
                    continue; // Saltar si no tiene grupo
                }

                // TODO: Verificar si el alumno se reinscribió
                // Por ahora, asumimos que NO se reinscribieron

                // Cambiar estatus según el grado
                if ($gradoActual == 1 || $gradoActual == 2) {
                    // 1° y 2° → Baja
                    $alumno->update(['estatus' => 'baja']);
                    
                    // Quitar grupo
                    if ($alumno->historialActual) {
                        $alumno->historialActual->update(['grupo_id' => null]);
                    }
                } elseif ($gradoActual == 3) {
                    // 3° → Egresado
                    $alumno->update(['estatus' => 'egresado']);
                    
                    // Quitar grupo
                    if ($alumno->historialActual) {
                        $alumno->historialActual->update(['grupo_id' => null]);
                    }
                }
            }

            // Desactivar el cuestionario
            $cuestionario->update(['activo' => false]);

            DB::commit();

            return redirect()->route('admin.inscripciones.reinscripcion')
                ->with('success', 'Reinscripciones cerradas exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al cerrar reinscripciones: ' . $e->getMessage()]);
        }
    }

    private function validarGruposDisponibles(array $datos): ?string
    {
        $nombresGrado = [1 => 'primero', 2 => 'segundo', 3 => 'tercero'];
        $gradosSinGrupos = [];

        foreach ($nombresGrado as $grado => $nombre) {
            $campoActivo = $nombre . '_activo';

            if (!empty($datos[$campoActivo]) && !Grupo::where('grado', $grado)->exists()) {
                $gradosSinGrupos[] = $nombre;
            }
        }

        if (empty($gradosSinGrupos)) {
            return null;
        }

        $lista = implode(', ', $gradosSinGrupos);
        return "No hay grupos creados para: {$lista}. Crea al menos un grupo para ese grado antes de publicar el formulario.";
    }
}

