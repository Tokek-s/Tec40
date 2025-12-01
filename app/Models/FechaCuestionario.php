<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FechaCuestionario extends Model
{
    use HasFactory;

    protected $table = 'cuestionarios_inscrip_reinscrip';

    public $timestamps = false;

    protected $fillable = [
        'titulo',
        'descripcion',
        'link_primero',
        'link_segundo',
        'link_tercero',
        'fecha_inicio',
        'fecha_fin',
        'activo',
        'primero_activo',
        'segundo_activo',
        'tercero_activo',
        'tipo',
    ];

	protected $casts = [
		'fecha_inicio' => 'date',
		'fecha_fin' => 'date',
		'activo' => 'boolean',
		'primero_activo' => 'boolean',
		'segundo_activo' => 'boolean',
		'tercero_activo' => 'boolean',
	];

	// Scope para registros vigentes hoy y activos
	public function scopeVigentes(Builder $query): Builder
	{
		$hoy = Carbon::today()->toDateString();
		return $query->where('activo', 1)
			->where('fecha_inicio', '<=', $hoy)
			->where('fecha_fin', '>=', $hoy)
			->orderBy('fecha_inicio', 'desc');
	}

	// Scope para filtrar por tipo
	public function scopeTipo(Builder $query, string $tipo): Builder
	{
		return $query->where('tipo', $tipo);
	}
}

