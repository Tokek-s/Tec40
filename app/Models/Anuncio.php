<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Anuncio extends Model
{
	protected $table = 'anuncios';

	protected $fillable = [
		'titulo',
		'contenido',
		'activo',
		'ruta_imagen',
		'archivo_multimedia_id',
		'fecha',
	];

	protected $casts = [
		'activo' => 'boolean',
		'fecha' => 'date',
	];

	public $timestamps = false;

	// Relación con archivo multimedia
	public function archivoMultimedia()
	{
		return $this->belongsTo(ArchivoMultimedia::class, 'archivo_multimedia_id');
	}

	// Scopes
	public function scopeActive(Builder $query): Builder
	{
		return $query->where('activo', 1);
	}

	public function scopeRecent(Builder $query, int $limit = 5): Builder
	{
		return $query->orderBy('fecha', 'desc')->limit($limit);
	}

	// Helpers
	public function getImageUrlAttribute(): ?string
	{
		// Priorizar archivo_multimedia_id sobre ruta_imagen (legacy)
		if ($this->archivo_multimedia_id && $this->archivoMultimedia) {
			return $this->archivoMultimedia->data_url;
		}
		return $this->ruta_imagen ? asset('storage/' . $this->ruta_imagen) : null;
	}
}

