<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use App\Models\ContactoAlumno;
use App\Models\SalidaAnticipada;
use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SalidasAnticipadasController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Admin/Docs/AutorizacionSalida', [
            'hoy' => now()->toDateString(),
        ]);
    }

    public function buscarAlumno(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        if ($q === '') return response()->json([]);

        $alumnos = Alumno::query()
            ->where('estatus', 'activo')
                        ->where(function($s) use ($q){
                                $s->where('curp', 'like', "%$q%")
                                    ->orWhere('matricula', 'like', "%$q%")
                                    ->orWhere('nombre_s', 'like', "%$q%")
                                    ->orWhere('apellido_paterno', 'like', "%$q%")
                                    ->orWhere('apellido_materno', 'like', "%$q%");
                        })
            ->limit(10)->get(['id','curp','matricula','nombre_s','apellido_paterno','apellido_materno']);

        return response()->json($alumnos->map(function($a){
            return [
                'id' => $a->id,
                'curp' => $a->curp,
                'matricula' => $a->matricula,
                'nombre' => trim($a->nombre_s.' '.$a->apellido_paterno.' '.$a->apellido_materno),
            ];
        }));
    }

    public function contactosAlumno(Alumno $alumno)
    {
        $contactos = ContactoAlumno::where('alumno_id', $alumno->id)
            ->where('autorizado_recoger', 1)
            ->limit(3)
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'nombre' => $c->nombre_completo,
                'parentesco' => $c->parentesco,
            ]);
        return response()->json($contactos);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'alumno_id' => ['required','integer','exists:alumnos,id'],
            'contacto_id' => ['nullable','integer','exists:contactos_alumno,id'],
            'motivo' => ['required','string','max:1000'],
            'firma' => ['required','string'], // dataURL PNG (tutor) - ahora obligatorio
            'fecha' => ['required','date'],
            'hora' => ['required','string'],
            'recoge_nombre' => ['required','string'],
            'recoge_parentesco' => ['required','string'],
        ]);

        $fechaCarbon = \Carbon\Carbon::parse($data['fecha']);
        $fecha = $fechaCarbon->toDateString();
        $hora = $data['hora'];

        // Normalizar firma del tutor
        $firmaDataUri = preg_match('/^data:/', $data['firma']) ? $data['firma'] : 'data:image/png;base64,' . base64_encode(base64_decode(preg_replace('/^data:image\/(png|jpeg);base64,/', '', $data['firma'])));

        // Resolver ID numérico del usuario autenticado
        $usuario = auth()->user();
        $autorizadoId = $usuario?->id;
        if (!$autorizadoId && $usuario && property_exists($usuario, 'correo') && !empty($usuario->correo)) {
            $autorizadoId = \App\Models\Usuario::where('correo', $usuario->correo)->value('id');
        }
        if (!$autorizadoId) {
            return response()->json([
                'ok' => false,
                'message' => 'No se pudo identificar al usuario que autoriza (ID). Vuelve a iniciar sesión e intenta de nuevo.'
            ], 422);
        }

        // Crear registro SalidaAnticipada
        $salida = SalidaAnticipada::create([
            'alumno_id' => $data['alumno_id'],
            'fecha' => $fecha,
            'hora_salida' => $hora,
            'motivo' => $data['motivo'],
            'autorizado_por_id' => (int) $autorizadoId,
            'entregado_a_contacto_id' => $data['contacto_id'] ?? null,
        ]);

        // Crear documento provisional con firma del tutor y datos de quien recoge
        try {
            $metadata = [
                'tutor' => $firmaDataUri,
                'recoge_nombre' => $data['recoge_nombre'],
                'recoge_parentesco' => $data['recoge_parentesco'],
            ];

            Documento::create([
                'entidad_id' => $salida->id,
                'entidad_tipo' => 'SalidaAnticipada',
                'tipo_documento' => 'Autorización_Recogida',
                'ruta_archivo' => '',
                'firma' => json_encode($metadata),
                'creado_por_id' => $autorizadoId,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Error creando documento provisional en DB', ['error' => $e->getMessage()]);
        }

        // Responder con URL del preview donde se firmará como quien recoge
        return response()->json([
            'ok' => true,
            'salida_id' => $salida->id,
            'preview_url' => route('admin.docs.salidas_preview', ['salida' => $salida->id]),
        ]);
    }

    /**
     * Mostrar la vista de previsualización/autorizar donde quien recoge puede firmar.
     */
    public function preview(SalidaAnticipada $salida): Response
    {
        $salida->load(['alumno','entregadoA','autorizadoPor']);
        $documento = Documento::where('entidad_id', $salida->id)
            ->where('entidad_tipo', 'SalidaAnticipada')
            ->where('tipo_documento', 'Autorización_Recogida')
            ->first();

        $firmaTutor = null;
        $recoge_nombre = null;
        $recoge_parentesco = null;
        $tutor_nombre = null;
        $tutor_parentesco = null;
        
        if ($documento && $documento->firma) {
            $decoded = json_decode($documento->firma, true) ?? [];
            $firmaTutor = $decoded['tutor'] ?? null;
            $recoge_nombre = $decoded['recoge_nombre'] ?? null;
            $recoge_parentesco = $decoded['recoge_parentesco'] ?? null;
        }

        // Obtener nombre del tutor/contacto que autorizó (si hay contacto relacionado, usar ese; sino usar usuario del sistema)
        if ($salida->entregadoA) {
            $tutor_nombre = $salida->entregadoA->nombre_completo ?? '';
            $tutor_parentesco = $salida->entregadoA->parentesco ?? '';
        } else {
            // Si no hay contacto, usar el usuario autenticado como tutor
            $tutor_nombre = $salida->autorizadoPor?->nombre_completo ?? '';
            $tutor_parentesco = 'Tutor';
        }

        $alumno_nombre = trim(($salida->alumno->nombre_s ?? '') . ' ' . ($salida->alumno->apellido_paterno ?? '') . ' ' . ($salida->alumno->apellido_materno ?? ''));

        // Obtener grado y grupo del historial actual o de columnas directas
        $historialActual = $salida->alumno->historialActual;
        $grado = $historialActual && $historialActual->grupo ? $historialActual->grupo->grado : $salida->alumno->grado;
        $grupo = $historialActual && $historialActual->grupo ? $historialActual->grupo->clave : $salida->alumno->grupo;

        return Inertia::render('Admin/Docs/SalidaPreview', [
            'salida' => [
                'id' => $salida->id,
                'fecha' => $salida->fecha ? \Carbon\Carbon::parse($salida->fecha)->format('d/m/Y') : '',
                'hora' => $salida->hora_salida,
                'motivo' => $salida->motivo,
                'alumno_nombre' => $alumno_nombre,
                'alumno_matricula' => $salida->alumno->matricula ?? '',
                'grado' => $grado,
                'grupo' => $grupo,
                'tutor_nombre' => $tutor_nombre,
                'tutor_parentesco' => $tutor_parentesco,
                'recoge_nombre' => $recoge_nombre,
                'recoge_parentesco' => $recoge_parentesco,
                'firma_tutor' => $firmaTutor,
            ],
        ]);
    }

    /**
     * Finalizar la autorización guardando la firma de quien recoge y generando el PDF final.
     */
    public function finalize(Request $request, SalidaAnticipada $salida)
    {
        $data = $request->validate([
            'firma_recoge' => ['required','string'],
        ]);

        $documento = Documento::where('entidad_id', $salida->id)
            ->where('entidad_tipo', 'SalidaAnticipada')
            ->where('tipo_documento', 'Autorización_Recogida')
            ->firstOrFail();

        // Normalizar data URI
        $firmaRecogeDataUri = preg_match('/^data:/', $data['firma_recoge']) ? $data['firma_recoge'] : 'data:image/png;base64,' . base64_encode(base64_decode(preg_replace('/^data:image\/(png|jpeg);base64,/', '', $data['firma_recoge'])));

        // Preparar datos para la plantilla y generar PDF
        $salida->load(['alumno.historialActual.grupo','entregadoA']);
        $alumno = $salida->alumno;
        $contacto = $salida->entregadoA;

        $fechaObj = \Carbon\Carbon::parse($salida->fecha);
        $dia = $fechaObj->format('d');
        $anio = $fechaObj->format('Y');
        $mesesEs = ['01'=>'enero','02'=>'febrero','03'=>'marzo','04'=>'abril','05'=>'mayo','06'=>'junio','07'=>'julio','08'=>'agosto','09'=>'septiembre','10'=>'octubre','11'=>'noviembre','12'=>'diciembre'];
        $mes = $mesesEs[$fechaObj->format('m')] ?? $fechaObj->format('F');

    $decodedDoc = $documento->firma ? (json_decode($documento->firma, true) ?? []) : [];
    
    // Nombre textual del tutor (quien autoriza) — usar el contacto (entregadoA) si existe, sino el usuario del sistema
    $tutor_nombre = null;
    $tutor_parentesco = null;
    if ($salida->entregadoA) {
        $tutor_nombre = $salida->entregadoA->nombre_completo ?? '';
        $tutor_parentesco = $salida->entregadoA->parentesco ?? '';
    } else {
        $tutor_nombre = $salida->autorizadoPor?->nombre_completo ?? '';
        $tutor_parentesco = 'Tutor';
    }
    
    // Nombre/parentesco de quien recoge: preferir los valores enviados en el formulario (almacenados en el JSON del documento)
    $recoge_nombre = $decodedDoc['recoge_nombre'] ?? $salida->entregadoA?->nombre_completo ?? null;
    $recoge_parentesco = $decodedDoc['recoge_parentesco'] ?? $salida->entregadoA?->parentesco ?? null;

        // Obtener grado y grupo del historial actual o de columnas directas
        $historialActual = $alumno->historialActual;
        $grado = $historialActual && $historialActual->grupo ? $historialActual->grupo->grado : $alumno->grado;
        $grupo = $historialActual && $historialActual->grupo ? $historialActual->grupo->clave : $alumno->grupo;

        // Convertir imagen de fondo a data URI para dompdf
        $fondoPath = public_path('images/doc-fondo.png');
        $fondoDataUri = '';
        if (file_exists($fondoPath)) {
            $imageData = file_get_contents($fondoPath);
            $base64 = base64_encode($imageData);
            $fondoDataUri = 'data:image/png;base64,' . $base64;
        }

        $html = view('autorizacion_salida.template', [
            'tutor_nombre' => $tutor_nombre,
            'parentesco' => $tutor_parentesco ?? '',
            'alumno_nombre' => trim(($alumno->nombre_s ?? '') . ' ' . ($alumno->apellido_paterno ?? '') . ' ' . ($alumno->apellido_materno ?? '')),
            'grado' => $grado,
            'grupo' => $grupo,
            'dia' => $dia,
            'mes' => $mes,
            'anio' => $anio,
            'hora' => $salida->hora_salida,
            'recoge_nombre' => $recoge_nombre,
            'recoge_parentesco' => $recoge_parentesco,
            'motivo' => $salida->motivo,
            'firma_tutor' => ($documento->firma ? ($decodedDoc['tutor'] ?? null) : null),
            'firma_recoge' => $firmaRecogeDataUri,
            'fondo_image' => $fondoDataUri,
        ])->render();

        $pdfPath = 'salidas/'.date('Y/m').'/salida_'.$salida->id.'.pdf';
        $pdfFullDir = dirname(Storage::disk('public')->path($pdfPath));
        if (!is_dir($pdfFullDir)) { mkdir($pdfFullDir, 0775, true); }

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        Storage::disk('public')->put($pdfPath, $dompdf->output());

        // Actualizar documento con ruta y agregar firma recoge al JSON
        try {
            $firmas = [];
            if ($documento->firma) {
                $firmas = json_decode($documento->firma, true) ?? [];
            }
            $firmas['recoge'] = $firmaRecogeDataUri;
            $documento->ruta_archivo = $pdfPath;
            $documento->firma = json_encode($firmas);
            $documento->save();
        } catch (\Throwable $e) {
            \Log::error('Error actualizando documento final', ['error' => $e->getMessage()]);
        }

        return response()->json([
            'ok' => true,
            'pdf_url' => asset('storage/'.$pdfPath),
            'pdf_view_url' => route('admin.docs.salidas_pdf', ['salida' => $salida->id]),
        ]);
    }

    public function index(Request $request): Response
    {
        $alumnoId = $request->query('alumno_id');
        $fecha = $request->query('fecha');

        $q = SalidaAnticipada::query()->with(['alumno','entregadoA']);
        if ($alumnoId) $q->where('alumno_id', $alumnoId);
        if ($fecha) $q->where('fecha', $fecha);

        $salidas = $q->orderByDesc('fecha')->orderByDesc('id')->paginate(10)->through(function($s){
            $pdfPath = 'salidas/'.optional($s->fecha)->format('Y').'/'.optional($s->fecha)->format('m').'/salida_'.$s->id.'.pdf';
            return [
                'id' => $s->id,
                'alumno' => $s->alumno?->nombre_s.' '.$s->alumno?->apellido_paterno.' '.$s->alumno?->apellido_materno,
                'contacto' => $s->entregadoA?->nombre_completo,
                'fecha' => optional($s->fecha)->format('Y-m-d'),
                'hora_salida' => $s->hora_salida,
                'motivo' => $s->motivo,
                'pdf_url' => Storage::disk('public')->exists($pdfPath) ? asset('storage/'.$pdfPath) : null,
            ];
        });

        return Inertia::render('Admin/Docs/SalidasIndex', [
            'salidas' => $salidas,
            'filters' => [
                'alumno_id' => $alumnoId,
                'fecha' => $fecha,
            ],
        ]);
    }

    public function showPdf(SalidaAnticipada $salida)
    {
        $year = optional($salida->fecha)->format('Y');
        $month = optional($salida->fecha)->format('m');
        $pdfPath = "salidas/{$year}/{$month}/salida_{$salida->id}.pdf";
        $full = Storage::disk('public')->path($pdfPath);
        if (!is_file($full)) {
            abort(404);
        }
        return response()->file($full, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="salida_'.$salida->id.'.pdf"'
        ]);
    }

    public function borrarPdf($id)
    {
        $salida = SalidaAnticipada::findOrFail($id);
        
        $year = optional($salida->fecha)->format('Y');
        $month = optional($salida->fecha)->format('m');
        $pdfPath = "salidas/{$year}/{$month}/salida_{$salida->id}.pdf";
        
        // Borrar el archivo PDF si existe
        if (Storage::disk('public')->exists($pdfPath)) {
            Storage::disk('public')->delete($pdfPath);
        }
        
        // Borrar la firma si existe
        if (isset($salida->ruta_firma) && Storage::disk('public')->exists($salida->ruta_firma)) {
            Storage::disk('public')->delete($salida->ruta_firma);
        }
        
        // Actualizar el registro para limpiar las rutas
        try {
            $salida->ruta_pdf = null;
            $salida->ruta_firma = null;
            $salida->save();
        } catch (\Throwable $e) {
            // Ignorar si las columnas no existen
        }
        
        return redirect()->back()->with('success', 'PDF borrado exitosamente');
    }

    public function eliminar($id)
    {
        $salida = SalidaAnticipada::findOrFail($id);
        
        $year = optional($salida->fecha)->format('Y');
        $month = optional($salida->fecha)->format('m');
        $pdfPath = "salidas/{$year}/{$month}/salida_{$salida->id}.pdf";
        
        // Borrar el archivo PDF si existe
        if (Storage::disk('public')->exists($pdfPath)) {
            Storage::disk('public')->delete($pdfPath);
        }
        
        // Borrar la firma si existe
        if (isset($salida->ruta_firma) && Storage::disk('public')->exists($salida->ruta_firma)) {
            Storage::disk('public')->delete($salida->ruta_firma);
        }
        
        // Eliminar el registro de la base de datos
        $salida->delete();
        
        return redirect()->back()->with('success', 'Salida eliminada exitosamente');
    }
}
