<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatosExtra extends Model
{
    protected $table = 'datos_extra';
    
    protected $guarded = [];
    
    public function alumno()
    {
        return $this->belongsTo(Alumno::class);
    }
}
