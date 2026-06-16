<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();
                $rol = $user->rol ?? null;
                
                // Redirigir según el rol del usuario
                if (in_array($rol, ['Prefecto', 'Prefectos'])) {
                    return redirect()->route('prefectos.dashboard');
                }
                
                // Roles administrativos
                $rolesAdmin = ['Sistemas', 'Direccion', 'Subdireccion', 'Administrativo', 'Medico', 'Psicologo'];
                if (in_array($rol, $rolesAdmin)) {
                    return redirect()->route('admin.dashboard');
                }
                
                // Por defecto, redirigir al home
                return redirect('/');
            }
        }

        return $next($request);
    }
}
