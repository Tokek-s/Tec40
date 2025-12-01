<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Alumno;
use App\Models\ContactoAlumno;
use App\Models\ArchivoMultimedia;
use Illuminate\Support\Facades\Storage;

class MigrarImagenesADatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'imagenes:migrar-a-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migra las imágenes del filesystem a la base de datos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando migración de imágenes a la base de datos...');
        
        // Migrar fotos de alumnos
        $this->info("\n=== Migrando fotos de alumnos ===");
        $alumnosConFoto = Alumno::whereNotNull('foto_path')->get();
        $alumnosMigrados = 0;
        
        foreach ($alumnosConFoto as $alumno) {
            $rutaCompleta = storage_path('app/public/' . $alumno->foto_path);
            
            if (file_exists($rutaCompleta)) {
                try {
                    $contenido = file_get_contents($rutaCompleta);
                    $extension = pathinfo($alumno->foto_path, PATHINFO_EXTENSION);
                    $mimeType = $this->getMimeType($extension);
                    
                    $archivo = ArchivoMultimedia::create([
                        'nombre_archivo' => basename($alumno->foto_path),
                        'tipo_mime' => $mimeType,
                        'tipo_archivo' => 'foto_alumno',
                        'tamano' => strlen($contenido),
                        'contenido' => $contenido,
                    ]);
                    
                    // Actualizar alumno con el ID del archivo
                    $alumno->archivo_multimedia_id = $archivo->id;
                    $alumno->save();
                    
                    $alumnosMigrados++;
                    $this->info("✓ {$alumno->nombre_completo} - {$alumno->matricula}");
                } catch (\Exception $e) {
                    $this->error("✗ Error en {$alumno->matricula}: " . $e->getMessage());
                }
            } else {
                $this->warn("⚠ Archivo no encontrado: {$alumno->foto_path}");
            }
        }
        
        $this->info("\nAlumnos migrados: {$alumnosMigrados}/{$alumnosConFoto->count()}");
        
        // Migrar fotos de contactos (ya no hay fotos antiguas, se guardarán directamente en DB)
        $this->info("\n=== Fotos de contactos ===");
        $this->info("Las fotos de contactos se guardarán directamente en la base de datos desde ahora.");
        
        $this->info("\n✅ Migración completada!");
        $this->info("Total de archivos migrados: " . $alumnosMigrados);
        
        return 0;
    }
    
    private function getMimeType($extension)
    {
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
        ];
        
        return $mimeTypes[strtolower($extension)] ?? 'image/jpeg';
    }
}
