<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogCsrfToken
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->method() === 'POST') {
            Log::info('CSRF Debug', [
                'url' => $request->url(),
                'session_token' => $request->session()->token(),
                'request_token' => $request->input('_token'),
                'header_token' => $request->header('X-CSRF-TOKEN'),
                'xsrf_token' => $request->header('X-XSRF-TOKEN'),
                'cookies' => array_keys($request->cookies->all()),
            ]);
        }
        
        return $next($request);
    }
}
