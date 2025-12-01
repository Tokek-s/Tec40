<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CuestionarioExterno;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FormulariosController extends Controller
{
    public function index(): Response
    {
        $formularios = CuestionarioExterno::orderByDesc('id')->get();
        return Inertia::render('Admin/Formularios/Index', [
            'formularios' => $formularios,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Formularios/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'link_primero' => 'nullable|string|url',
            'link_segundo' => 'nullable|string|url',
            'link_tercero' => 'nullable|string|url',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'activo' => 'boolean',
            'primero_activo' => 'boolean',
            'segundo_activo' => 'boolean',
            'tercero_activo' => 'boolean',
        ]);

        CuestionarioExterno::create($validated);

        return redirect()->route('admin.formularios.index')->with('success', 'Formulario externo creado correctamente.');
    }

    public function edit($id): Response
    {
        $formulario = CuestionarioExterno::findOrFail($id);
        return Inertia::render('Admin/Formularios/Edit', [
            'formulario' => $formulario,
        ]);
    }

    public function update(Request $request, $id)
    {
        $formulario = CuestionarioExterno::findOrFail($id);

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'link_primero' => 'nullable|string|url',
            'link_segundo' => 'nullable|string|url',
            'link_tercero' => 'nullable|string|url',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'activo' => 'boolean',
            'primero_activo' => 'boolean',
            'segundo_activo' => 'boolean',
            'tercero_activo' => 'boolean',
        ]);

        $formulario->update($validated);

        return redirect()->route('admin.formularios.index')->with('success', 'Formulario externo actualizado correctamente.');
    }

    public function destroy($id)
    {
        $formulario = CuestionarioExterno::findOrFail($id);
        $formulario->delete();

        return redirect()->back()->with('success', 'Formulario externo eliminado correctamente.');
    }
}
