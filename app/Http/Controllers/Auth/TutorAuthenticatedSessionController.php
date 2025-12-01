<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Alumno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class TutorAuthenticatedSessionController extends Controller
{
    public function create()
    {
        return Inertia::render('Tutor/LoginTutor');
    }

    public function store(Request $request)
    {
        // Validar input: CURP y matrícula
        $validated = $request->validate([
            'curp' => ['required', 'string', 'size:18'],
            'matricula' => ['required', 'string', 'max:20'],
        ], [
            'curp.required' => 'La CURP es obligatoria',
            'curp.size' => 'La CURP debe tener 18 caracteres',
            'matricula.required' => 'La matrícula es obligatoria',
        ]);

        // Normalizar a mayúsculas
        $curp = strtoupper($validated['curp']);
        $matricula = strtoupper($validated['matricula']);

        // Buscar alumno que coincida
        $alumno = Alumno::query()
            ->whereRaw('UPPER(curp) = ?', [$curp])
            ->whereRaw('UPPER(matricula) = ?', [$matricula])
            ->first();

        if (!$alumno) {
            return Redirect::back()->withErrors([
                'curp' => 'Las credenciales no coinciden con nuestros registros',
            ])->withInput();
        }

    // Regenerar y guardar referencia en sesión
    $request->session()->regenerate();
    Session::put('tutor_alumno_id', $alumno->id);

        return Redirect::route('tutor.dashboard');
    }

    public function destroy(Request $request)
    {
        Session::forget('tutor_alumno_id');
        $request->session()->regenerateToken();
        return Redirect::route('home');
    }
}
