<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Si es una petición a rutas de admin o prefectos, redirigir al login de admin
        if ($request->is('admin/*') || $request->is('prefectos/*') || $request->is('asistencias/*')) {
            return route('admin.login');
        }

        // Si es una petición a rutas de tutor, redirigir al login de tutor
        if ($request->is('tutor/*')) {
            return route('tutor.login');
        }

        // Por defecto, redirigir al home
        return $request->expectsJson() ? null : route('home');
    }
}
