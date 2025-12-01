<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre_s',
        'apellido_paterno',
        'apellido_materno',
        'correo',
        'hash_contrasena',
        'rol',
        'activo',
    ];

    protected $hidden = [
        'hash_contrasena',
        'remember_token',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public $timestamps = true;

    public function getAuthPassword()
    {
        return $this->hash_contrasena;
    }

    public function getAuthIdentifierName()
    {
        return 'correo';
    }

    public function getNombreCompletoAttribute()
    {
        return trim("{$this->nombre_s} {$this->apellido_paterno} {$this->apellido_materno}");
    }

    public function isActive()
    {
        return $this->activo === 1;
    }

    public function isAdmin()
    {
        return in_array($this->rol, ['Direccion', 'Sistemas']);
    }

    public function scopeActive($query)
    {
        return $query->where('activo', 1);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('rol', $role);
    }
}
