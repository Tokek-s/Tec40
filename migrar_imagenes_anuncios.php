<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Anuncio;
use App\Models\ArchivoMultimedia;

echo "=== Migrando imágenes de anuncios a archivos_multimedia ===\n\n";

try {
    // Obtener todos los anuncios con ruta_imagen pero sin archivo_multimedia_id
    $anuncios = Anuncio::whereNotNull('ruta_imagen')
        ->whereNull('archivo_multimedia_id')
        ->get();
    
    if ($anuncios->isEmpty()) {
        echo "✅ No hay anuncios por migrar.\n";
        exit(0);
    }
    
    echo "📋 Encontrados {$anuncios->count()} anuncios con imágenes por migrar.\n\n";
    
    $migrados = 0;
    $errores = 0;
    
    foreach ($anuncios as $anuncio) {
        echo "Procesando anuncio #{$anuncio->id} - {$anuncio->titulo}...\n";
        
        // Verificar si el archivo existe
        $rutaCompleta = storage_path('app/public/' . $anuncio->ruta_imagen);
        
        if (file_exists($rutaCompleta)) {
            // Crear registro en archivos_multimedia
            $archivo = ArchivoMultimedia::create([
                'ruta_archivo' => $anuncio->ruta_imagen,
                'tipo_archivo' => mime_content_type($rutaCompleta),
            ]);
            
            // Actualizar anuncio
            $anuncio->archivo_multimedia_id = $archivo->id;
            $anuncio->save();
            
            echo "  ✅ Migrado a archivo_multimedia #{$archivo->id}\n";
            $migrados++;
        } else {
            echo "  ⚠️  Archivo no encontrado: {$anuncio->ruta_imagen}\n";
            $errores++;
        }
    }
    
    echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✅ Migración completada\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Migrados exitosamente: {$migrados}\n";
    echo "Errores (archivos no encontrados): {$errores}\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
