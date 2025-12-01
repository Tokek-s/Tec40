<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;

class Grupo extends Model
{
    use HasFactory;

    protected $table = 'grupos';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'grado',
        'clave',
        'turno',
    ];

    protected static function booted()
    {
        // Cuando se crea un nuevo grupo, intentar asignar alumnos sin grupo
        static::created(function ($grupo) {
            try {
                Artisan::call('alumnos:asignar-grupos');
            } catch (\Exception $e) {
                \Log::warning('No se pudo ejecutar asignación automática de grupos: ' . $e->getMessage());
            }
        });
    }

    // Relación con historial académico
    public function historialAcademico()
    {
        return $this->hasMany(HistorialAcademico::class, 'grupo_id');
    }
}
