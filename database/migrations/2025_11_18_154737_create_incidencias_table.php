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
        Schema::create('incidencias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alumno_id');
            $table->text('descripcion');
            $table->unsignedBigInteger('registrado_por_id');
            $table->date('fecha');
            $table->string('firma_docente')->nullable(); // Data URL de la firma del docente
            $table->string('firma_tutor')->nullable(); // Data URL de la firma del tutor
            $table->string('pdf_path')->nullable(); // Ruta del PDF final
            $table->timestamp('creado_en')->useCurrent();
            
            $table->foreign('alumno_id')->references('id')->on('alumnos')->onDelete('cascade');
            $table->foreign('registrado_por_id')->references('id')->on('usuarios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidencias');
    }
};
