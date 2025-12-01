<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalidaAnticipada extends Model
{
    protected $table = 'salidas_anticipadas';
    public $timestamps = false;

    protected $fillable = [
        'alumno_id',
        'fecha',
        'hora_salida',
        'motivo',
        'autorizado_por_id',
        'entregado_a_contacto_id',
        'ruta_pdf',
        'ruta_firma',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function alumno() { return $this->belongsTo(Alumno::class, 'alumno_id'); }
    public function autorizadoPor() { return $this->belongsTo(Usuario::class, 'autorizado_por_id'); }
    public function entregadoA() { return $this->belongsTo(ContactoAlumno::class, 'entregado_a_contacto_id'); }
}
