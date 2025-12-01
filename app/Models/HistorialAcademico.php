<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialAcademico extends Model
{
    use HasFactory;

    protected $table = 'historial_academico';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'alumno_id',
        'grupo_id',
        'ciclo',
        'estatus',
    ];

    // Relación con alumno
    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }

    // Relación con grupo
    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }
}
