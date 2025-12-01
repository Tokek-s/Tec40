<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuestionarioExterno extends Model
{
    protected $table = 'cuestionarios_externos';

    protected $fillable = [
        'titulo',
        'descripcion',
        'link_primero',
        'link_segundo',
        'link_tercero',
        'fecha_inicio',
        'fecha_fin',
        'activo',
        'primero_activo',
        'segundo_activo',
        'tercero_activo',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
        'primero_activo' => 'boolean',
        'segundo_activo' => 'boolean',
        'tercero_activo' => 'boolean',
    ];
}
