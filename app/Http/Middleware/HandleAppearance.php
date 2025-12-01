<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sincroniza el modo de apariencia (light/dark) entre cookie y request.
 * Lee la cookie 'appearance' y la expone a Inertia a través de shared props
 * en HandleInertiaRequests (que ya está configurado en el proyecto).
 */
class HandleAppearance
{
	public function handle(Request $request, Closure $next): Response
	{
		// Asegura que exista la cookie 'appearance' con valor por defecto
		$appearance = $request->cookie('appearance');
		if (!in_array($appearance, ['light', 'dark'], true)) {
			// No forzamos cookie aquí para evitar headers extra en cada request;
			// el front establece la cookie con initializeTheme().
		}

		// Permite que otros middlewares/Controllers lean el valor si quisieran
		$request->attributes->set('appearance', $appearance ?: 'system');

		return $next($request);
	}
}

