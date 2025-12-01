<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anuncio;
use App\Models\ArchivoMultimedia;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AnuncioController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->query('q', ''));

        $query = Anuncio::query();
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                  ->orWhere('contenido', 'like', "%{$search}%");
            });
        }

        $anuncios = $query->with('archivoMultimedia')->orderByDesc('fecha')->orderByDesc('id')->paginate(10)->through(function ($a) {
            return [
                'id' => $a->id,
                'titulo' => $a->titulo,
                'contenido' => $a->contenido,
                'fecha' => optional($a->fecha)->format('Y-m-d'),
                'activo' => (bool) $a->activo,
                'ruta_imagen' => $a->ruta_imagen,
                'archivo_multimedia_id' => $a->archivo_multimedia_id,
                'image_url' => $a->image_url,
            ];
        });

        return Inertia::render('Admin/Anuncios/Index', [
            'anuncios' => $anuncios,
            'filters' => [ 'q' => $search ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Anuncios/CreateEdit', [
            'mode' => 'create',
            'anuncio' => null,
        ]);
    }

    public function edit(Anuncio $anuncio): Response
    {
        $anuncio->load('archivoMultimedia');
        
        return Inertia::render('Admin/Anuncios/CreateEdit', [
            'mode' => 'edit',
            'anuncio' => [
                'id' => $anuncio->id,
                'titulo' => $anuncio->titulo,
                'contenido' => $anuncio->contenido,
                'fecha' => optional($anuncio->fecha)->format('Y-m-d'),
                'activo' => (bool) $anuncio->activo,
                'ruta_imagen' => $anuncio->ruta_imagen,
                'archivo_multimedia_id' => $anuncio->archivo_multimedia_id,
                'image_url' => $anuncio->image_url,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'contenido' => ['nullable', 'string'],
            'fecha' => ['nullable', 'date'],
            'activo' => ['sometimes', 'boolean'],
            'imagen' => ['nullable', 'image', 'max:5120'], // 5MB máximo
        ]);

        $archivoMultimediaId = null;
        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $nombreArchivo = 'anuncio_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Guardar imagen como BLOB en la base de datos
            $archivo = ArchivoMultimedia::create([
                'nombre_archivo' => $nombreArchivo,
                'tipo_mime' => $file->getMimeType(),
                'tipo_archivo' => 'imagen_anuncio',
                'tamano' => $file->getSize(),
                'contenido' => file_get_contents($file->getRealPath()),
            ]);
            
            $archivoMultimediaId = $archivo->id;
        }

        $anuncio = Anuncio::create([
            'titulo' => $data['titulo'],
            'contenido' => $data['contenido'] ?? null,
            'fecha' => $data['fecha'] ?? now()->toDateString(),
            'activo' => (int) ($data['activo'] ?? 1),
            'archivo_multimedia_id' => $archivoMultimediaId,
        ]);

        return redirect()->route('admin.anuncios.index')->with('success', 'Anuncio creado');
    }

    public function update(Request $request, Anuncio $anuncio)
    {
        $data = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'contenido' => ['nullable', 'string'],
            'fecha' => ['nullable', 'date'],
            'activo' => ['sometimes', 'boolean'],
            'imagen' => ['nullable', 'image', 'max:5120'], // 5MB máximo
            'remove_imagen' => ['sometimes', 'boolean'],
        ]);

        // Remover imagen
        if (!empty($data['remove_imagen'])) {
            if ($anuncio->archivo_multimedia_id) {
                $archivo = $anuncio->archivoMultimedia;
                if ($archivo) {
                    // Eliminar registro de base de datos
                    $archivo->delete();
                }
            }
            $anuncio->archivo_multimedia_id = null;
        }

        // Nueva imagen
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($anuncio->archivo_multimedia_id) {
                $archivo = $anuncio->archivoMultimedia;
                if ($archivo) {
                    $archivo->delete();
                }
            }
            
            // Guardar nueva imagen como BLOB
            $file = $request->file('imagen');
            $nombreArchivo = 'anuncio_' . $anuncio->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            $archivo = ArchivoMultimedia::create([
                'nombre_archivo' => $nombreArchivo,
                'tipo_mime' => $file->getMimeType(),
                'tipo_archivo' => 'imagen_anuncio',
                'tamano' => $file->getSize(),
                'contenido' => file_get_contents($file->getRealPath()),
            ]);
            
            $anuncio->archivo_multimedia_id = $archivo->id;
        }

        $anuncio->titulo = $data['titulo'];
        $anuncio->contenido = $data['contenido'] ?? null;
        $anuncio->fecha = $data['fecha'] ?? $anuncio->fecha;
        $anuncio->activo = (int) ($data['activo'] ?? $anuncio->activo);
        $anuncio->save();

        return redirect()->route('admin.anuncios.index')->with('success', 'Anuncio actualizado');
    }

    public function destroy(Anuncio $anuncio)
    {
        // Eliminar archivo multimedia si existe
        if ($anuncio->archivo_multimedia_id) {
            $archivo = $anuncio->archivoMultimedia;
            if ($archivo) {
                $archivo->delete();
            }
        }
        
        $anuncio->delete();
        return redirect()->route('admin.anuncios.index')->with('success', 'Anuncio eliminado');
    }
}
