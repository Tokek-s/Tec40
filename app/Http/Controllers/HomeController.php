<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\FechaCuestionario;
use App\Models\CuestionarioExterno;
use App\Models\Anuncio;

class HomeController extends Controller
{
    public function index(): Response
    {
        // Anuncios activos (si la tabla existe)
        $anuncios = collect();
        try {
            if (DB::getSchemaBuilder()->hasTable('anuncios')) {
                $anuncios = Anuncio::query()
                    ->active()
                    ->recent(5)
                    ->get()
                    ->map(function (Anuncio $a) {
                        return [
                            'id' => $a->id,
                            'titulo' => $a->titulo,
                            'contenido' => $a->contenido,
                            'ruta_imagen' => $a->ruta_imagen,
                            'image_url' => $a->image_url,
                            'fecha' => optional($a->fecha)->format('Y-m-d'),
                            'activo' => (bool) $a->activo,
                        ];
                    });
            }
        } catch (\Throwable $e) {
            $anuncios = collect();
        }

        // Cuestionarios vigentes usando el modelo (inscripciones)
        $cuestionariosVigentes = collect();
        try {
            if (DB::getSchemaBuilder()->hasTable('cuestionarios_inscrip_reinscrip')) {
                $cuestionariosVigentes = FechaCuestionario::query()
                    ->where('tipo', 'inscripcion')
                    ->orderByDesc('fecha_inicio')
                    ->get()
                    ->map(function ($c) {
                        $hoy = now()->toDateString();
                        $inicio = $c->fecha_inicio ? $c->fecha_inicio->toDateString() : null;
                        $fin = $c->fecha_fin ? $c->fecha_fin->toDateString() : null;
                        
                        $vigente = ($c->activo) && ($inicio && $inicio <= $hoy) && ($fin && $fin >= $hoy);
                        
                        // Generar links automáticos solo si el grado está activo
                        $linkPrimero = $c->primero_activo ? route('formulario.inscripcion', ['tipo' => 'primero']) : null;
                        $linkSegundo = $c->segundo_activo ? route('formulario.inscripcion', ['tipo' => 'segundo']) : null;
                        $linkTercero = $c->tercero_activo ? route('formulario.inscripcion', ['tipo' => 'tercero']) : null;
                        
                        return [
                            'id' => $c->id,
                            'titulo' => $c->titulo,
                            'descripcion' => $c->descripcion,
                            'link_primero' => $linkPrimero,
                            'link_segundo' => $linkSegundo,
                            'link_tercero' => $linkTercero,
                            'fecha_inicio' => optional($c->fecha_inicio)->format('Y-m-d'),
                            'fecha_fin' => optional($c->fecha_fin)->format('Y-m-d'),
                            'activo' => (bool) $c->activo,
                            'primero_activo' => (bool) $c->primero_activo,
                            'segundo_activo' => (bool) $c->segundo_activo,
                            'tercero_activo' => (bool) $c->tercero_activo,
                            'vigente' => (bool) $vigente,
                        ];
                    });
            }
        } catch (\Throwable $e) {
            $cuestionariosVigentes = collect();
        }

        // Cuestionarios de reinscripción
        $cuestionariosReinscripcion = collect();
        try {
            if (DB::getSchemaBuilder()->hasTable('cuestionarios_inscrip_reinscrip')) {
                $cuestionariosReinscripcion = FechaCuestionario::query()
                    ->where('tipo', 'reinscripcion')
                    ->orderByDesc('fecha_inicio')
                    ->get()
                    ->map(function ($c) {
                        $hoy = now()->toDateString();
                        $inicio = $c->fecha_inicio ? $c->fecha_inicio->toDateString() : null;
                        $fin = $c->fecha_fin ? $c->fecha_fin->toDateString() : null;
                        
                        $vigente = ($c->activo) && ($inicio && $inicio <= $hoy) && ($fin && $fin >= $hoy);
                        
                        // Para reinscripción, generar el link a la ruta de reinscripción
                        $linkPrimero = $c->primero_activo ? route('formulario.inscripcion', ['tipo' => 'reinscripcion']) : null;
                        $linkSegundo = $c->segundo_activo ? route('formulario.inscripcion', ['tipo' => 'reinscripcion']) : null;
                        $linkTercero = $c->tercero_activo ? route('formulario.inscripcion', ['tipo' => 'reinscripcion']) : null;
                        
                        return [
                            'id' => $c->id,
                            'titulo' => $c->titulo,
                            'descripcion' => $c->descripcion,
                            'link_primero' => $linkPrimero,
                            'link_segundo' => $linkSegundo,
                            'link_tercero' => $linkTercero,
                            'fecha_inicio' => optional($c->fecha_inicio)->format('Y-m-d'),
                            'fecha_fin' => optional($c->fecha_fin)->format('Y-m-d'),
                            'activo' => (bool) $c->activo,
                            'primero_activo' => (bool) $c->primero_activo,
                            'segundo_activo' => (bool) $c->segundo_activo,
                            'tercero_activo' => (bool) $c->tercero_activo,
                            'vigente' => (bool) $vigente,
                        ];
                    });
            }
        } catch (\Throwable $e) {
            $cuestionariosReinscripcion = collect();
        }

        // Log de verificación de conexión/conteo
        try {
            Log::info('home.cuestionarios_count', ['count' => $cuestionariosVigentes->count()]);
        } catch (\Throwable $e) {
            // Ignorar errores de log
        }

        // Cuestionarios Externos (NUEVO)
        $cuestionariosExternos = collect();
        try {
            if (DB::getSchemaBuilder()->hasTable('cuestionarios_externos')) {
                $cuestionariosExternos = CuestionarioExterno::query()->orderByDesc('fecha_inicio')->get()->map(function ($c) {
                    $hoy = now()->toDateString();
                    $inicio = $c->fecha_inicio ? $c->fecha_inicio->toDateString() : null;
                    $fin = $c->fecha_fin ? $c->fecha_fin->toDateString() : null;
                    
                    $vigente = ($c->activo) && ($inicio && $inicio <= $hoy) && ($fin && $fin >= $hoy);
                    
                    return [
                        'id' => $c->id,
                        'titulo' => $c->titulo,
                        'descripcion' => $c->descripcion,
                        'link_primero' => $c->link_primero,
                        'link_segundo' => $c->link_segundo,
                        'link_tercero' => $c->link_tercero,
                        'fecha_inicio' => optional($c->fecha_inicio)->format('Y-m-d'),
                        'fecha_fin' => optional($c->fecha_fin)->format('Y-m-d'),
                        'activo' => (bool) $c->activo,
                        'primero_activo' => (bool) $c->primero_activo,
                        'segundo_activo' => (bool) $c->segundo_activo,
                        'tercero_activo' => (bool) $c->tercero_activo,
                        'vigente' => (bool) $vigente,
                    ];
                });
            }
        } catch (\Throwable $e) {
            $cuestionariosExternos = collect();
        }

        return Inertia::render('Home/Index', [
            'anuncios' => $anuncios,
            'cuestionarios' => $cuestionariosVigentes,
            'cuestionariosReinscripcion' => $cuestionariosReinscripcion,
            'cuestionariosExternos' => $cuestionariosExternos,
        ]);
    }
}
