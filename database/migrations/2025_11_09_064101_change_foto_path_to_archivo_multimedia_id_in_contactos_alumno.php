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
        Schema::table('contactos_alumno', function (Blueprint $table) {
            // Solo agregar la columna si no existe
            if (!Schema::hasColumn('contactos_alumno', 'archivo_multimedia_id')) {
                $table->unsignedBigInteger('archivo_multimedia_id')->nullable();
                $table->foreign('archivo_multimedia_id')->references('id')->on('archivos_multimedia')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contactos_alumno', function (Blueprint $table) {
            // Eliminar foreign key y columna
            $table->dropForeign(['archivo_multimedia_id']);
            $table->dropColumn('archivo_multimedia_id');
            
            // Restaurar foto_path
            $table->string('foto_path', 255)->nullable();
        });
    }
};
