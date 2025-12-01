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
        Schema::create('archivos_multimedia', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_archivo', 255);
            $table->string('tipo_mime', 100);
            $table->string('tipo_archivo', 50); // 'foto_contacto', 'foto_alumno', etc.
            $table->integer('tamano'); // tamaño en bytes
            $table->binary('contenido'); // imagen guardada como BLOB
            $table->timestamp('creado_en')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archivos_multimedia');
    }
};
