<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/debug-headers', function (Request $request) {
    return response()->json([
        'headers' => $request->headers->all(),
        'ip' => $request->ip(),
        'secure' => $request->secure(),
        'scheme' => $request->getScheme(),
        'session_config' => config('session'),
        'url' => url('/'),
    ]);
});
