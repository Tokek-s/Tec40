<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('archivos_multimedia', function (Blueprint $table) {
            // Renombrar ruta_archivo a nombre_archivo
            $table->renameColumn('ruta_archivo', 'nombre_archivo');
            
            // Agregar columnas faltantes
            $table->string('tipo_mime', 100)->after('nombre_archivo');
            $table->integer('tamano')->after('tipo_archivo'); // tamaño en bytes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('archivos_multimedia', function (Blueprint $table) {
            // Revertir cambios
            $table->renameColumn('nombre_archivo', 'ruta_archivo');
            $table->dropColumn(['tipo_mime', 'tamano']);
        });
    }
};
