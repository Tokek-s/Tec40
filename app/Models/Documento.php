<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $table = 'documentos';
    
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = null;

    protected $fillable = [
        'entidad_id',
        'entidad_tipo',
        'tipo_documento',
        'ruta_archivo',
        'firma',
        'creado_por_id',
    ];

    protected $casts = [
        'creado_en' => 'datetime',
    ];

    public function creadoPor()
    {
        return $this->belongsTo(Usuario::class, 'creado_por_id');
    }

    // Relación polimórfica
    public function entidad()
    {
        return $this->morphTo('entidad', 'entidad_tipo', 'entidad_id');
    }
}
