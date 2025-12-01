<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchivoMultimedia extends Model
{
    protected $table = 'archivos_multimedia';
    
    public $timestamps = false;
    
    const CREATED_AT = 'creado_en';

    protected $fillable = [
        'nombre_archivo',
        'tipo_mime',
        'tipo_archivo',
        'tamano',
        'contenido',
    ];

    protected $hidden = [
        'contenido', // No incluir el BLOB en las respuestas JSON por defecto
    ];

    /**
     * Get the data URL for displaying the image
     */
    public function getDataUrlAttribute(): string
    {
        if ($this->contenido) {
            // Convertir BLOB a data URL
            $base64 = base64_encode($this->contenido);
            $mimeType = $this->tipo_mime ?? 'image/jpeg';
            return "data:{$mimeType};base64,{$base64}";
        }
        return asset('images/avatar-default.svg');
    }
    
    /**
     * Get the base64 encoded content
     */
    public function getBase64Attribute(): ?string
    {
        if ($this->contenido) {
            return base64_encode($this->contenido);
        }
        return null;
    }
}
