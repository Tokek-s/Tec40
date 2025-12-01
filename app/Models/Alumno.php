<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
	use HasFactory;

	// Tabla esperada en la BD
	protected $table = 'alumnos';

	// Suponemos llave primaria incremental id
	protected $primaryKey = 'id';

	// Muchos catálogos de alumnos no usan timestamps
	public $timestamps = false;

	// Campos de uso común para el portal de padres
	protected $fillable = [
		'curp',
		'matricula',
		'nombre',
		'nombre_s',
		'apellido_paterno',
		'apellido_materno',
		'fecha_nacimiento',
		'sexo',
		'generacion',
		'turno',
		'estatus',
		'archivo_multimedia_id',
	];

	protected $casts = [
		'fecha_nacimiento' => 'date',
	];

	// Accessor para obtener el nombre completo
	// Formato: Apellido Paterno + Apellido Materno + Nombre(s)
	public function getNombreCompletoAttribute()
	{
		return trim(
			($this->apellido_paterno ?? '') . ' ' .
			($this->apellido_materno ?? '') . ' ' .
			($this->nombre_s ?? '')
		);
	}

	// Relación con archivo multimedia (foto)
	public function archivoMultimedia()
	{
		return $this->belongsTo(ArchivoMultimedia::class, 'archivo_multimedia_id');
	}

	// Accessor para obtener la URL de la foto
	public function getFotoUrlAttribute()
	{
		if ($this->archivo_multimedia_id) {
			// Cargar solo los campos necesarios, excluyendo contenido
			$archivo = ArchivoMultimedia::select('id', 'nombre_archivo', 'tipo_mime', 'tipo_archivo', 'tamano', 'contenido')
				->find($this->archivo_multimedia_id);
			
			if ($archivo && $archivo->contenido) {
				$base64 = base64_encode($archivo->contenido);
				$mimeType = $archivo->tipo_mime ?? 'image/jpeg';
				return "data:{$mimeType};base64,{$base64}";
			}
		}
		
		return asset('images/avatar-default.svg');
	}

	// Relación con contactos
	public function contactos()
	{
		return $this->hasMany(ContactoAlumno::class, 'alumno_id');
	}

	// Relación con contacto principal (el primero registrado)
	public function contactoPrincipal()
	{
		return $this->hasOne(ContactoAlumno::class, 'alumno_id')->oldest('id');
	}

	// Relación con salud
	public function salud()
	{
		return $this->hasOne(Salud::class, 'alumno_id');
	}

	// Relación con historial académico
	public function historialAcademico()
	{
		return $this->hasMany(HistorialAcademico::class, 'alumno_id');
	}

	// Relación con el historial académico actual (el más reciente)
	public function historialActual()
	{
		return $this->hasOne(HistorialAcademico::class, 'alumno_id')
			->latest('id');
	}

	// Relación con grupo a través del historial actual
	public function grupoActual()
	{
		return $this->historialActual()->with('grupo');
	}

	// Relación con datos extra (preguntas adicionales de inscripción/reinscripción)
	public function datosExtra()
	{
		return $this->hasOne(DatosExtra::class, 'alumno_id');
	}
}

