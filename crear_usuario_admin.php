<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

try {
    // Generar hash de la contraseña
    $hash = Hash::make('admin123');
    
    // Insertar usuario
    DB::table('usuarios')->insert([
        'nombre_s' => 'Admin',
        'apellido_paterno' => 'Pruebas',
        'apellido_materno' => 'tec40',
        'correo' => 'sistemas@tec40.edu.mx',
        'hash_contrasena' => $hash,
        'rol' => 'Sistemas',
        'activo' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    echo "✅ Usuario creado exitosamente\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📧 Correo: sistemas@tec40.edu.mx\n";
    echo "🔑 Contraseña: admin123\n";
    echo "👤 Rol: Sistemas\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
