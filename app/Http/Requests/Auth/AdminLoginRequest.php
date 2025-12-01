<?php

namespace App\Http\Requests\Auth;

use App\Models\Usuario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'correo' => ['required', 'string', 'email'],
            'contrasena' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'El correo electrónico debe tener un formato válido.',
            'contrasena.required' => 'La contraseña es obligatoria.',
        ];
    }

    public function authenticate(): void
    {
        $correo = $this->input('correo');
        $contrasena = $this->input('contrasena');

        $usuario = Usuario::where('correo', $correo)->first();

        if (!$usuario) {
            throw ValidationException::withMessages([
                'correo' => 'No se encontró un usuario con ese correo electrónico.',
            ]);
        }

        // Permitir acceso a todos los roles administrativos
        $rolesPermitidos = ['Sistemas', 'Direccion', 'Subdireccion', 'Administrativo', 'Prefecto', 'Medico', 'Psicologo'];
        if (!in_array($usuario->rol, $rolesPermitidos)) {
            throw ValidationException::withMessages([
                'correo' => 'Este usuario no tiene permisos de administrador.',
            ]);
        }

        if (!Hash::check($contrasena, $usuario->hash_contrasena)) {
            throw ValidationException::withMessages([
                'correo' => 'La contraseña proporcionada es incorrecta.',
            ]);
        }

        Auth::login($usuario, $this->boolean('remember'));
    }
}
