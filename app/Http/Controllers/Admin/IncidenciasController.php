<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Incidencia;
use App\Models\Alumno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class IncidenciasController extends Controller
{
    public function index(Request $request)
    {
        $query = Incidencia::with(['alumno', 'registradoPor']);
        
        // Filtros
        if ($request->filled('alumno_id')) {
            $query->where('alumno_id', $request->alumno_id);
        }
        
        if ($request->filled('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        }
        
        $incidencias = $query->orderBy('fecha', 'desc')
            ->orderBy('creado_en', 'desc')
            ->paginate(15)
            ->through(function ($incidencia) {
                return [
                    'id' => $incidencia->id,
                    'fecha' => $incidencia->fecha->format('Y-m-d'),
                    'alumno' => $incidencia->alumno->nombre_completo,
                    'descripcion' => $incidencia->descripcion,
                    'registrado_por' => $incidencia->registradoPor->nombre_completo,
                    'pdf_url' => $incidencia->pdf_url,
                    'tiene_firma_tutor' => !empty($incidencia->firma_tutor),
                ];
            });
        
        return Inertia::render('Admin/Docs/IncidenciasIndex', [
            'incidencias' => $incidencias,
            'filters' => [
                'alumno_id' => $request->alumno_id,
                'fecha' => $request->fecha,
            ],
        ]);
    }
    
    public function create()
    {
        return Inertia::render('Admin/Docs/IncidenciaForm', [
            'hoy' => now()->format('Y-m-d'),
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'alumno_id' => 'required|exists:alumnos,id',
            'descripcion' => 'required|string',
            'medidas' => 'required|string',
            'area' => 'required|string',
            'nombre_docente' => 'required|string',
            'fecha' => 'required|date',
            'firma' => 'required|string'
        ]);
        
        // Guardar firma del docente
        $firmaData = $validated['firma'];
        $firmaData = str_replace('data:image/png;base64,', '', $firmaData);
        $firmaData = str_replace(' ', '+', $firmaData);
        $firmaDecoded = base64_decode($firmaData);
        $firmaName = 'firma_docente_' . time() . '_' . uniqid() . '.png';
        Storage::disk('public')->put('firmas/' . $firmaName, $firmaDecoded);
        $firmaPath = 'firmas/' . $firmaName;
        
        $incidencia = Incidencia::create([
            'alumno_id' => $validated['alumno_id'],
            'descripcion' => $validated['descripcion'],
            'medidas' => $validated['medidas'],
            'area' => $validated['area'],
            'nombre_docente_reporta' => $validated['nombre_docente'],
            'fecha' => $validated['fecha'],
            'registrado_por_id' => auth()->user()->id,
            'firma_docente' => $firmaPath
        ]);
        
        // Retornar URL de preview para que el tutor firme
        return response()->json([
            'ok' => true,
            'preview_url' => route('admin.docs.incidencias_preview', ['incidencia' => $incidencia->id]),
        ]);
    }
    
    public function preview($incidenciaId)
    {
        $incidencia = Incidencia::with(['alumno.historialActual.grupo', 'registradoPor'])->findOrFail($incidenciaId);
        
        $contactos = $incidencia->alumno->contactos->map(function($contacto) {
            return [
                'id' => $contacto->id,
                'nombre' => $contacto->nombre_completo,
                'parentesco' => $contacto->parentesco
            ];
        });

        return Inertia::render('Admin/Docs/IncidenciaPreview', [
            'incidencia' => [
                'id' => $incidencia->id,
                'fecha' => $incidencia->fecha->format('Y-m-d'),
                'alumno' => $incidencia->alumno->nombre_completo,
                'alumno_id' => $incidencia->alumno_id,
                'grado' => $incidencia->alumno->historialActual->grupo->grado ?? null,
                'grupo' => $incidencia->alumno->historialActual->grupo->clave ?? null,
                'descripcion' => $incidencia->descripcion,
                'medidas' => $incidencia->medidas,
                'area' => $incidencia->area,
                'nombre_docente_reporta' => $incidencia->nombre_docente_reporta,
                'registrado_por' => $incidencia->registradoPor->nombre_completo,
                'firma_docente' => $incidencia->firma_docente ? asset('storage/' . $incidencia->firma_docente) : null,
                'tiene_firma_tutor' => !empty($incidencia->firma_tutor),
            ],
            'contactos' => $contactos,
        ]);
    }
    
    public function finalize(Request $request, $incidenciaId)
    {
        $validated = $request->validate([
            'firma_tutor' => 'required|string',
            'nombre_tutor' => 'required|string',
        ]);
        
        $incidencia = Incidencia::with(['alumno.historialActual.grupo', 'registradoPor'])->findOrFail($incidenciaId);
        
        // Guardar firma del tutor
        $firmaTutorData = $validated['firma_tutor'];
        $firmaTutorData = str_replace('data:image/png;base64,', '', $firmaTutorData);
        $firmaTutorData = str_replace(' ', '+', $firmaTutorData);
        $firmaTutorDecoded = base64_decode($firmaTutorData);
        $firmaTutorName = 'firma_tutor_' . time() . '_' . uniqid() . '.png';
        Storage::disk('public')->put('firmas/' . $firmaTutorName, $firmaTutorDecoded);
        $firmaTutorPath = 'firmas/' . $firmaTutorName;
        
        $incidencia->firma_tutor = $firmaTutorPath;
        $incidencia->nombre_tutor_firma = $validated['nombre_tutor'];
        $incidencia->save();
        
        // Generar PDF
        $alumno = $incidencia->alumno;
        $historial = $alumno->historialActual;
        $grupo = $historial ? $historial->grupo : null;
        
        // Convertir firmas a base64 para el PDF
        $firmaDocenteBase64 = '';
        if ($incidencia->firma_docente && Storage::disk('public')->exists($incidencia->firma_docente)) {
            $firmaDocenteData = Storage::disk('public')->get($incidencia->firma_docente);
            $firmaDocenteBase64 = 'data:image/png;base64,' . base64_encode($firmaDocenteData);
        }
        
        $firmaTutorBase64 = '';
        if ($incidencia->firma_tutor && Storage::disk('public')->exists($incidencia->firma_tutor)) {
            $firmaTutorData = Storage::disk('public')->get($incidencia->firma_tutor);
            $firmaTutorBase64 = 'data:image/png;base64,' . base64_encode($firmaTutorData);
        }
        
        $data = [
            'fecha_formato' => $incidencia->fecha->locale('es')->isoFormat('D [de] MMMM [del] YYYY'),
            'fecha' => $incidencia->fecha->format('d/m/Y'),
            'alumno' => $alumno->nombre_completo,
            'grado' => $grupo ? $grupo->grado : '',
            'grupo' => $grupo ? $grupo->clave : '',
            'descripcion' => $incidencia->descripcion,
            'medidas' => $incidencia->medidas,
            'area' => $incidencia->area,
            'registrado_por' => $incidencia->registradoPor->nombre_completo,
            'nombre_docente_reporta' => $incidencia->nombre_docente_reporta,
            'nombre_tutor_firma' => $incidencia->nombre_tutor_firma,
            'firma_docente' => $firmaDocenteBase64,
            'firma_tutor' => $firmaTutorBase64,
        ];
        
        // Cargar imagen de fondo
        $fondoPath = public_path('images/doc-fondo.png');
        if (file_exists($fondoPath)) {
            $fondoData = base64_encode(file_get_contents($fondoPath));
            $data['fondo_base64'] = 'data:image/png;base64,' . $fondoData;
        }
        
        $pdf = Pdf::loadView('pdfs.incidencia', $data);
        $pdf->setPaper('letter', 'portrait');
        
        // Guardar PDF
        $fileName = 'incidencia_' . $incidencia->id . '_' . time() . '.pdf';
        $pdfPath = 'pdfs/incidencias/' . $fileName;
        Storage::disk('public')->put($pdfPath, $pdf->output());
        
        $incidencia->pdf_path = $pdfPath;
        $incidencia->save();
        
        return redirect()->back()->with([
            'pdf_url' => asset('storage/' . $pdfPath)
        ]);
    }
    
    public function borrarPDF($incidenciaId)
    {
        $incidencia = Incidencia::findOrFail($incidenciaId);
        
        if ($incidencia->pdf_path) {
            Storage::disk('public')->delete($incidencia->pdf_path);
            $incidencia->pdf_path = null;
            $incidencia->firma_tutor = null;
            $incidencia->save();
        }
        
        return redirect()->back()->with('success', 'PDF borrado exitosamente');
    }
    
    public function eliminar($incidenciaId)
    {
        $incidencia = Incidencia::findOrFail($incidenciaId);
        
        // Borrar PDF si existe
        if ($incidencia->pdf_path) {
            Storage::disk('public')->delete($incidencia->pdf_path);
        }
        
        $incidencia->delete();
        
        return redirect()->back()->with('success', 'Incidencia eliminada exitosamente');
    }
}
