<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incidencia extends Model
{
    protected $table = 'incidencias';
    
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = null;
    
    protected $fillable = [
        'alumno_id',
        'descripcion',
        'medidas',
        'area',
        'registrado_por_id',
        'fecha',
        'firma_docente',
        'nombre_docente_reporta',
        'firma_tutor',
        'nombre_tutor_firma',
        'pdf_path'
    ];
    
    protected $casts = [
        'fecha' => 'date',
        'creado_en' => 'datetime',
    ];
    
    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }
    
    public function registradoPor()
    {
        return $this->belongsTo(Usuario::class, 'registrado_por_id');
    }
    
    public function getPdfUrlAttribute()
    {
        return $this->pdf_path ? asset('storage/' . $this->pdf_path) : null;
    }
}
