<?php

// Script de prueba de sesión
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "=== Test de Configuración de Sesión ===\n\n";

echo "SESSION_DRIVER: " . env('SESSION_DRIVER', 'file') . "\n";
echo "SESSION_LIFETIME: " . env('SESSION_LIFETIME', 120) . "\n";
echo "SESSION_ENCRYPT: " . (env('SESSION_ENCRYPT', false) ? 'true' : 'false') . "\n\n";

echo "Config session.driver: " . config('session.driver') . "\n";
echo "Config session.lifetime: " . config('session.lifetime') . "\n\n";

// Verificar tabla de sesiones si usa database
if (config('session.driver') === 'database') {
    try {
        $count = DB::table('sessions')->count();
        echo "Sesiones activas en BD: $count\n";
    } catch (\Exception $e) {
        echo "ERROR: No se puede conectar a la tabla de sesiones: " . $e->getMessage() . "\n";
    }
}

echo "\n=== Middlewares Web Group ===\n";
$middlewareGroups = config('middleware.groups', []);
if (isset($middlewareGroups['web'])) {
    print_r($middlewareGroups['web']);
}

echo "\n✅ Test completado\n";
