<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ValidatePaseListaAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }
            return redirect()->route('admin.login');
        }
        $rol = optional(Auth::user())->rol ?? '';
        if (!in_array($rol, ['Sistemas', 'Direccion', 'Prefecto', 'Prefectos'])) {
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json(['message' => 'Forbidden'], 403);
            }
            abort(403);
        }
        return $next($request);
    }
}
