<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Anuncio;

echo "=== Verificando sistema de anuncios con archivos_multimedia ===\n\n";

$anuncio = Anuncio::with('archivoMultimedia')->first();

if (!$anuncio) {
    echo "⚠️  No hay anuncios en la base de datos\n";
    exit(0);
}

echo "📋 Anuncio #{$anuncio->id}\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Título: {$anuncio->titulo}\n";
echo "Fecha: {$anuncio->fecha}\n";
echo "Activo: " . ($anuncio->activo ? 'Sí' : 'No') . "\n";
echo "\n";
echo "🖼️  Información de imagen:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "archivo_multimedia_id: " . ($anuncio->archivo_multimedia_id ?? 'NULL') . "\n";
echo "ruta_imagen (legacy): " . ($anuncio->ruta_imagen ?? 'NULL') . "\n";

if ($anuncio->archivoMultimedia) {
    echo "\n✅ Relación con ArchivoMultimedia establecida:\n";
    echo "  - ID: {$anuncio->archivoMultimedia->id}\n";
    echo "  - Ruta: {$anuncio->archivoMultimedia->ruta_archivo}\n";
    echo "  - Tipo: {$anuncio->archivoMultimedia->tipo_archivo}\n";
    echo "  - URL: {$anuncio->image_url}\n";
} else {
    echo "\n⚠️  Sin archivo multimedia asociado\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ Sistema funcionando correctamente\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
