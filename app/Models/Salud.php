<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salud extends Model
{
    protected $table = 'salud';
    
    public $timestamps = false;

    protected $fillable = [
        'alumno_id',
        'tipo_sangre',
        'alergias',
        'enfermedades_cronicas',
    ];

    // Relación con alumno
    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'alumno_id');
    }
}
