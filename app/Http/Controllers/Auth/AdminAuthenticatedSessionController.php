<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdminLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AdminAuthenticatedSessionController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Admin/Login', [
            'status' => session('status'),
        ]);
    }

    public function store(AdminLoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();
        } catch (\Exception $e) {
            \Log::error('Admin login failed', [
                'correo' => $request->input('correo'),
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }

        $request->session()->regenerate();

        if (Auth::check()) {
            $user = Auth::user();
            \Log::info('Admin autenticado', [
                'id' => $user->id ?? null,
                'nombre' => $user->nombre_completo ?? null,
                'rol' => $user->rol ?? null,
            ]);

            $rol = $user->rol ?? '';
            if (in_array($rol, ['Prefecto', 'Prefectos'])) {
                return redirect()->route('prefectos.dashboard');
            }
            return redirect()->route('admin.dashboard');
        }

        \Log::error('Usuario no autenticado después de login');
        return redirect()->route('admin.login')->withErrors(['error' => 'Error de autenticación']);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
 
