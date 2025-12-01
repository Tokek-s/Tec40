<?php

namespace App\Http\Controllers;

use App\Models\FechaCuestionario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Abort;
use Inertia\Inertia;

class InscripcionController extends Controller
{
    public function show(string $grado, int $cuestionario)
    {
        $c = FechaCuestionario::findOrFail($cuestionario);

        $hoy = now()->toDateString();
        $vigente = ($c->activo) && ($c->fecha_inicio <= $hoy) && ($c->fecha_fin >= $hoy);

        // Validar flag por grado
        $flag = match ($grado) {
            'primero' => (bool) $c->primero_activo,
            'segundo' => (bool) $c->segundo_activo,
            'tercero' => (bool) $c->tercero_activo,
            default => false,
        };

        if (! $vigente || ! $flag) {
            abort(404);
        }

        // Aquí puedes renderizar una página del cuestionario o redirigir a un formulario externo
        return Inertia::render('Inscripciones/Show', [
            'grado' => $grado,
            'cuestionario' => [
                'id' => $c->id,
                'titulo' => $c->titulo,
                'descripcion' => $c->descripcion,
                'fecha_inicio' => optional($c->fecha_inicio)->format('Y-m-d'),
                'fecha_fin' => optional($c->fecha_fin)->format('Y-m-d'),
            ],
        ]);
    }
}
