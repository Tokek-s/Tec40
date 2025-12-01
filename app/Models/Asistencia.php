<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use HasFactory;

    protected $table = 'asistencias';
    
    public $timestamps = false;

    protected $fillable = [
        'alumno_id',
        'grupo_id',
        'fecha',
        'estado',
        'registrado_por',
    ];
    
    protected $guarded = [];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    public function registradoPor()
    {
        return $this->belongsTo(Usuario::class, 'registrado_por');
    }
}
