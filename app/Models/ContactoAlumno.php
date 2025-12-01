<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactoAlumno extends Model
{
    protected $table = 'contactos_alumno';
    public $timestamps = false;

    protected $fillable = [
        'alumno_id',
        'nombre_s',
        'apellido_paterno',
        'apellido_materno',
        'parentesco',
        'telefono',
        'correo',
        'autorizado_recoger',
        'archivo_multimedia_id',
    ];

    protected $casts = [
        'autorizado_recoger' => 'boolean',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }

    public function archivoMultimedia()
    {
        return $this->belongsTo(ArchivoMultimedia::class, 'archivo_multimedia_id');
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim(
            ($this->apellido_paterno ?? '') . ' ' .
            ($this->apellido_materno ?? '') . ' ' .
            ($this->nombre_s ?? '')
        );
    }

    public function getFotoUrlAttribute(): ?string
    {
        if ($this->archivo_multimedia_id) {
            $archivo = ArchivoMultimedia::select('id', 'nombre_archivo', 'tipo_mime', 'tipo_archivo', 'tamano', 'contenido')
                ->find($this->archivo_multimedia_id);
            
            if ($archivo && $archivo->contenido) {
                $base64 = base64_encode($archivo->contenido);
                $mimeType = $archivo->tipo_mime ?? 'image/jpeg';
                return "data:{$mimeType};base64,{$base64}";
            }
        }
        return null;
    }
}
