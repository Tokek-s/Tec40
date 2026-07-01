<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FechaCuestionario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class InscripcionesController extends Controller
{
    public function index(): Response
    {
        $cuestionarios = FechaCuestionario::orderByDesc('id')->get()->map(function ($cuestionario) {
            // Contar alumnos inscritos por grado usando join con grupos
            $cicloActual = $this->obtenerCicloEscolarActual();
            
            // Contar alumnos por grado desde historial_academico con join a grupos
            $inscritosPrimero = DB::table('historial_academico')
                ->join('grupos', 'historial_academico.grupo_id', '=', 'grupos.id')
                ->where('grupos.grado', 'primero')
                ->where('historial_academico.generacion', $cicloActual)
                ->where('historial_academico.estatus', 'inscrito')
                ->count();

            $inscritosSegundo = DB::table('historial_academico')
                ->join('grupos', 'historial_academico.grupo_id', '=', 'grupos.id')
                ->where('grupos.grado', 'segundo')
                ->where('historial_academico.generacion', $cicloActual)
                ->where('historial_academico.estatus', 'inscrito')
                ->count();

            $inscritosTercero = DB::table('historial_academico')
                ->join('grupos', 'historial_academico.grupo_id', '=', 'grupos.id')
                ->where('grupos.grado', 'tercero')
                ->where('historial_academico.generacion', $cicloActual)
                ->where('historial_academico.estatus', 'inscrito')
                ->count();
            
            return array_merge($cuestionario->toArray(), [
                'inscritos_primero' => $inscritosPrimero,
                'inscritos_segundo' => $inscritosSegundo,
                'inscritos_tercero' => $inscritosTercero,
            ]);
        });
        
        return Inertia::render('Admin/Inscripciones/Inscripcion', [
            'cuestionarios' => $cuestionarios,
        ]);
    }

    public function crear(): Response
    {
        $camposExtra = $this->obtenerCamposExtra();
        
        return Inertia::render('Admin/Inscripciones/CrearEditar', [
            'camposExtra' => $camposExtra,
        ]);
    }

    public function editar($id): Response
    {
        $cuestionario = FechaCuestionario::findOrFail($id);
        $camposExtra = $this->obtenerCamposExtra();
        
        return Inertia::render('Admin/Inscripciones/CrearEditar', [
            'cuestionario' => $cuestionario,
            'camposExtra' => $camposExtra,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'activo' => 'boolean',
            'primero_activo' => 'boolean',
            'segundo_activo' => 'boolean',
            'tercero_activo' => 'boolean',
            'campos_extra' => 'array',
            'campos_extra.*.nombre' => 'required|string|max:255',
            'campos_extra.*.tipo' => 'required|string|in:texto',
        ]);

        // Generar título automático
        $anioActual = date('Y');
        $validated['titulo'] = "Inscripciones {$anioActual}";

        // Crear el cuestionario
        $cuestionario = FechaCuestionario::create($validated);

        // Agregar campos extra a la tabla datos_extra
        if (!empty($validated['campos_extra'])) {
            $this->agregarCamposExtra($validated['campos_extra']);
        }

        return redirect()->route('admin.inscripciones.inscripcion')->with('success', 'Cuestionario de inscripción creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $cuestionario = FechaCuestionario::findOrFail($id);

        $validated = $request->validate([
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'activo' => 'boolean',
            'primero_activo' => 'boolean',
            'segundo_activo' => 'boolean',
            'tercero_activo' => 'boolean',
        ]);

        $cuestionario->update($validated);

        return redirect()->route('admin.inscripciones.inscripcion')->with('success', 'Cuestionario actualizado correctamente.');
    }

    public function destroy($id)
    {
        $cuestionario = FechaCuestionario::findOrFail($id);
        $cuestionario->delete();

        return redirect()->back()->with('success', 'Cuestionario eliminado correctamente.');
    }

    public function agregarCampoExtra(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'tipo' => 'required|string',
        ]);

        $nombreCampo = str_replace(' ', '_', strtolower($request->nombre));
        $nombreCampo = preg_replace('/[^a-z0-9_]/', '', $nombreCampo);

        try {
            // Verificar si la columna ya existe
            $existe = DB::select("SHOW COLUMNS FROM datos_extra LIKE '{$nombreCampo}'");
            
            if (!empty($existe)) {
                return response()->json(['error' => 'El campo ya existe'], 400);
            }

            DB::statement("ALTER TABLE datos_extra ADD COLUMN `{$nombreCampo}` TEXT NULL");
            
            return response()->json(['message' => 'Campo agregado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al agregar el campo: ' . $e->getMessage()], 500);
        }
    }

    public function eliminarCampoExtra(Request $request)
    {
        $request->validate([
            'nombre_campo' => 'required|string',
            'password' => 'required|string',
        ]);

        // Verificar contraseña del usuario autenticado
        $usuario = auth()->user();
        if (!Hash::check($request->password, $usuario->hash_contrasena)) {
            return response()->json(['error' => 'Contraseña incorrecta'], 403);
        }

        // Eliminar la columna de la tabla datos_extra
        try {
            DB::statement("ALTER TABLE datos_extra DROP COLUMN `{$request->nombre_campo}`");
            
            return response()->json(['message' => 'Campo eliminado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar el campo: ' . $e->getMessage()], 500);
        }
    }

    private function obtenerCamposExtra(): array
    {
        try {
            $columns = DB::select("SHOW COLUMNS FROM datos_extra");
            $camposExtra = [];
            
            foreach ($columns as $column) {
                // Excluir las columnas del sistema (id, alumno_id, timestamps)
                if (!in_array($column->Field, ['id', 'alumno_id', 'created_at', 'updated_at'])) {
                    $camposExtra[] = [
                        'nombre' => $column->Field,
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
        // Obtener el año actual (mes > julio = ciclo actual, mes <= julio = ciclo anterior)
        $anio = now()->month > 7 ? now()->year : now()->year - 1;
        return $anio . '-' . ($anio + 1);
    }

    private function agregarCamposExtra(array $campos): void
    {
        foreach ($campos as $campo) {
            $nombreCampo = str_replace(' ', '_', strtolower($campo['nombre']));
            $nombreCampo = preg_replace('/[^a-z0-9_]/', '', $nombreCampo);
            
            try {
                // Verificar si la columna ya existe
                $existe = DB::select("SHOW COLUMNS FROM datos_extra LIKE '{$nombreCampo}'");
                
                if (empty($existe)) {
                    DB::statement("ALTER TABLE datos_extra ADD COLUMN `{$nombreCampo}` TEXT NULL");
                }
            } catch (\Exception $e) {
                // Log error pero continuar con los demás campos
                \Log::error("Error al agregar campo extra: " . $e->getMessage());
            }
        }
    }
}
