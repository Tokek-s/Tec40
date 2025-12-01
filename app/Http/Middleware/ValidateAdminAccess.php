<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ValidateAdminAccess
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
	 */
	public function handle(Request $request, Closure $next): Response
	{
		if (!Auth::check()) {
			// Usuario no autenticado: enviar al login de admin
			return redirect()->route('admin.login');
		}

		// Validar que el usuario tenga un rol administrativo
		$user = Auth::user();
		$rol = $user->rol ?? null;
		
		// Los prefectos tienen su propia área, no deben acceder al área de admin
		if (in_array($rol, ['Prefecto', 'Prefectos'])) {
			return redirect()->route('prefectos.dashboard');
		}
		
		// Solo permitir roles administrativos
		if (!in_array($rol, ['Sistemas', 'Direccion'])) {
			abort(403, 'No tienes permisos para acceder al área de administración.');
		}

		return $next($request);
	}
}

