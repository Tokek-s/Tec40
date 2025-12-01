<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UsuariosController extends Controller
{
    /**
     * Mostrar lista de usuarios con filtros
     */
    public function index(Request $request): Response
    {
        // Solo Direccion y Sistemas pueden acceder
        $user = auth()->user();
        if (!in_array($user->rol, ['Direccion', 'Sistemas'])) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $busqueda = $request->query('busqueda');
        $rol = $request->query('rol');

        $query = Usuario::query();

        // Filtro por búsqueda (nombre o correo)
        if ($busqueda) {
            $query->where(function($q) use ($busqueda) {
                $q->where('nombre_s', 'like', "%{$busqueda}%")
                  ->orWhere('apellido_paterno', 'like', "%{$busqueda}%")
                  ->orWhere('apellido_materno', 'like', "%{$busqueda}%")
                  ->orWhere('correo', 'like', "%{$busqueda}%");
            });
        }

        // Filtro por rol
        if ($rol && $rol !== 'todos') {
            $query->where('rol', $rol);
        }

        $usuarios = $query->orderBy('nombre_s')
            ->orderBy('apellido_paterno')
            ->paginate(15)
            ->through(function($usuario) {
                return [
                    'id' => $usuario->id,
                    'nombre_completo' => $usuario->nombre_completo,
                    'correo' => $usuario->correo,
                    'rol' => $usuario->rol,
                    'activo' => $usuario->activo,
                ];
            });

        return Inertia::render('Admin/Usuarios/Index', [
            'usuarios' => $usuarios,
            'filtros' => [
                'busqueda' => $busqueda,
                'rol' => $rol,
            ],
            'roles' => ['Direccion', 'Subdireccion', 'Administrativo', 'Prefecto', 'Sistemas', 'Medico', 'Psicologo'],
        ]);
    }

    /**
     * Crear nuevo usuario
     */
    public function store(Request $request)
    {
        // Solo Direccion y Sistemas pueden crear usuarios
        $user = auth()->user();
        if (!in_array($user->rol, ['Direccion', 'Sistemas'])) {
            abort(403);
        }

        $validated = $request->validate([
            'nombre_s' => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['nullable', 'string', 'max:100'],
            'correo' => ['required', 'email', 'unique:usuarios,correo', 'max:150'],
            'contrasena' => ['required', 'string', 'min:8'],
            'rol' => ['required', Rule::in(['Direccion', 'Subdireccion', 'Administrativo', 'Prefecto', 'Sistemas', 'Medico', 'Psicologo'])],
            'activo' => ['boolean'],
        ]);

        Usuario::create([
            'nombre_s' => $validated['nombre_s'],
            'apellido_paterno' => $validated['apellido_paterno'],
            'apellido_materno' => $validated['apellido_materno'] ?? null,
            'correo' => $validated['correo'],
            'hash_contrasena' => Hash::make($validated['contrasena']),
            'rol' => $validated['rol'],
            'activo' => $validated['activo'] ?? true,
        ]);

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Usuario $usuario): Response
    {
        // Solo Direccion y Sistemas pueden editar usuarios
        $user = auth()->user();
        if (!in_array($user->rol, ['Direccion', 'Sistemas'])) {
            abort(403);
        }

        return Inertia::render('Admin/Usuarios/Edit', [
            'usuario' => [
                'id' => $usuario->id,
                'nombre_s' => $usuario->nombre_s,
                'apellido_paterno' => $usuario->apellido_paterno,
                'apellido_materno' => $usuario->apellido_materno,
                'correo' => $usuario->correo,
                'rol' => $usuario->rol,
                'activo' => $usuario->activo,
            ],
            'roles' => ['Direccion', 'Subdireccion', 'Administrativo', 'Prefecto', 'Sistemas', 'Medico', 'Psicologo'],
        ]);
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, Usuario $usuario)
    {
        // Solo Direccion y Sistemas pueden actualizar usuarios
        $user = auth()->user();
        if (!in_array($user->rol, ['Direccion', 'Sistemas'])) {
            abort(403);
        }

        $validated = $request->validate([
            'nombre_s' => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['nullable', 'string', 'max:100'],
            'correo' => ['required', 'email', 'max:150', Rule::unique('usuarios')->ignore($usuario->id)],
            'contrasena' => ['nullable', 'string', 'min:8'],
            'rol' => ['required', Rule::in(['Direccion', 'Subdireccion', 'Administrativo', 'Prefecto', 'Sistemas', 'Medico', 'Psicologo'])],
            'activo' => ['boolean'],
        ]);

        $usuario->update([
            'nombre_s' => $validated['nombre_s'],
            'apellido_paterno' => $validated['apellido_paterno'],
            'apellido_materno' => $validated['apellido_materno'] ?? null,
            'correo' => $validated['correo'],
            'rol' => $validated['rol'],
            'activo' => $validated['activo'] ?? $usuario->activo,
        ]);

        // Actualizar contraseña solo si se proporcionó una nueva
        if (!empty($validated['contrasena'])) {
            $usuario->update([
                'hash_contrasena' => Hash::make($validated['contrasena']),
            ]);
        }

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Eliminar usuario
     */
    public function destroy(Usuario $usuario)
    {
        // Solo Direccion y Sistemas pueden eliminar usuarios
        $user = auth()->user();
        if (!in_array($user->rol, ['Direccion', 'Sistemas'])) {
            abort(403);
        }

        // No permitir eliminar el propio usuario
        if ($usuario->id === $user->id) {
            return back()->withErrors(['error' => 'No puedes eliminar tu propio usuario.']);
        }

        $usuario->delete();

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado exitosamente.');
    }
}
