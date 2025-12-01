<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactoAlumno;
use App\Models\Alumno;
use Illuminate\Http\Request;

class ContactosController extends Controller
{
    // Crear nuevo contacto
    public function store(Request $request, $alumnoId)
    {
        $validated = $request->validate([
            'nombre_s' => 'required|string|max:80',
            'apellido_paterno' => 'required|string|max:50',
            'apellido_materno' => 'required|string|max:50',
            'parentesco' => 'required|string|max:40',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:120',
            'autorizado_recoger' => 'boolean',
            'es_principal' => 'boolean',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $alumno = Alumno::findOrFail($alumnoId);

        // Si se marca como principal, quitar el principal anterior
        if ($validated['es_principal'] ?? false) {
            ContactoAlumno::where('alumno_id', $alumnoId)
                ->update(['es_principal' => false]);
        }

        $data = [
            'alumno_id' => $alumnoId,
            'nombre_s' => $validated['nombre_s'],
            'apellido_paterno' => $validated['apellido_paterno'],
            'apellido_materno' => $validated['apellido_materno'],
            'parentesco' => $validated['parentesco'],
            'telefono' => $validated['telefono'] ?? null,
            'correo' => $validated['correo'] ?? null,
            'autorizado_recoger' => $validated['autorizado_recoger'] ?? false,
            'es_principal' => $validated['es_principal'] ?? false,
        ];

        // Guardar foto si se proporciona
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $parentescoSlug = strtolower(str_replace(' ', '-', $validated['parentesco']));
            $nombreArchivo = $alumno->matricula . '-' . $parentescoSlug . '.' . $file->getClientOriginalExtension();
            
            // Crear registro en archivos_multimedia
            $archivoMultimedia = \App\Models\ArchivoMultimedia::create([
                'nombre_archivo' => $nombreArchivo,
                'tipo_mime' => $file->getMimeType(),
                'tipo_archivo' => 'foto_contacto',
                'tamano' => $file->getSize(),
                'contenido' => file_get_contents($file->getRealPath()),
            ]);
            
            $data['archivo_multimedia_id'] = $archivoMultimedia->id;
        }

        ContactoAlumno::create($data);

        return response()->json(['message' => 'Contacto agregado correctamente'], 201);
    }

    // Actualizar contacto
    public function update(Request $request, $id)
    {
        $contacto = ContactoAlumno::findOrFail($id);

        $validated = $request->validate([
            'nombre_s' => 'required|string|max:80',
            'apellido_paterno' => 'required|string|max:50',
            'apellido_materno' => 'required|string|max:50',
            'parentesco' => 'required|string|max:40',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:120',
            'autorizado_recoger' => 'boolean',
            'es_principal' => 'boolean',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Si se marca como principal, quitar el principal anterior
        if ($validated['es_principal'] ?? false) {
            ContactoAlumno::where('alumno_id', $contacto->alumno_id)
                ->where('id', '!=', $id)
                ->update(['es_principal' => false]);
        }

        $data = [
            'nombre_s' => $validated['nombre_s'],
            'apellido_paterno' => $validated['apellido_paterno'],
            'apellido_materno' => $validated['apellido_materno'],
            'parentesco' => $validated['parentesco'],
            'telefono' => $validated['telefono'] ?? null,
            'correo' => $validated['correo'] ?? null,
            'autorizado_recoger' => $validated['autorizado_recoger'] ?? false,
            'es_principal' => $validated['es_principal'] ?? false,
        ];

        // Guardar foto si se proporciona
        if ($request->hasFile('foto')) {
            // Eliminar foto anterior si existe
            if ($contacto->archivo_multimedia_id) {
                $archivoAnterior = \App\Models\ArchivoMultimedia::find($contacto->archivo_multimedia_id);
                if ($archivoAnterior) {
                    $archivoAnterior->delete();
                }
            }

            $file = $request->file('foto');
            $alumno = $contacto->alumno;
            $parentescoSlug = strtolower(str_replace(' ', '-', $validated['parentesco']));
            $nombreArchivo = $alumno->matricula . '-' . $parentescoSlug . '.' . $file->getClientOriginalExtension();
            
            // Crear registro en archivos_multimedia
            $archivoMultimedia = \App\Models\ArchivoMultimedia::create([
                'nombre_archivo' => $nombreArchivo,
                'tipo_mime' => $file->getMimeType(),
                'tipo_archivo' => 'foto_contacto',
                'tamano' => $file->getSize(),
                'contenido' => file_get_contents($file->getRealPath()),
            ]);
            
            $data['archivo_multimedia_id'] = $archivoMultimedia->id;
        }

        $contacto->update($data);

        return response()->json(['message' => 'Contacto actualizado correctamente']);
    }

    // Marcar como principal
    public function marcarPrincipal($id)
    {
        $contacto = ContactoAlumno::findOrFail($id);

        // Quitar el principal anterior
        ContactoAlumno::where('alumno_id', $contacto->alumno_id)
            ->update(['es_principal' => false]);

        // Marcar este como principal
        $contacto->update(['es_principal' => true]);

        return response()->json(['message' => 'Contacto marcado como principal']);
    }

    // Toggle activo/inactivo
    public function toggleActivo($id)
    {
        $contacto = ContactoAlumno::findOrFail($id);
        
        $nuevoEstado = !($contacto->activo ?? true);
        $contacto->update(['activo' => $nuevoEstado]);

        return response()->json([
            'message' => $nuevoEstado ? 'Contacto activado' : 'Contacto desactivado',
            'activo' => $nuevoEstado
        ]);
    }

    // Eliminar contacto
    public function destroy($id)
    {
        $contacto = ContactoAlumno::findOrFail($id);

        // No permitir eliminar si es el contacto principal
        if ($contacto->es_principal) {
            return response()->json([
                'error' => 'No se puede eliminar el contacto principal'
            ], 422);
        }

        $contacto->delete();

        return response()->json(['message' => 'Contacto eliminado correctamente']);
    }
}
