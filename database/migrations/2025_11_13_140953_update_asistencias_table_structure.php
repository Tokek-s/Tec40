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
        Schema::table('asistencias', function (Blueprint $table) {
            // Agregar grupo_id si no existe
            if (!Schema::hasColumn('asistencias', 'grupo_id')) {
                $table->unsignedBigInteger('grupo_id')->nullable()->after('alumno_id');
                $table->foreign('grupo_id')->references('id')->on('grupos')->onDelete('cascade');
            }
            
            // Agregar estado si no existe
            if (!Schema::hasColumn('asistencias', 'estado')) {
                $table->enum('estado', ['presente', 'falta', 'retardo', 'justificado'])->default('presente')->after('fecha');
            } else {
                // Modificar el ENUM para incluir 'justificado' si ya existe
                DB::statement("ALTER TABLE asistencias MODIFY COLUMN estado ENUM('presente', 'falta', 'retardo', 'justificado') DEFAULT 'presente'");
            }
            
            // Agregar registrado_por si no existe
            if (!Schema::hasColumn('asistencias', 'registrado_por')) {
                $table->unsignedBigInteger('registrado_por')->nullable()->after('estado');
                $table->foreign('registrado_por')->references('id')->on('usuarios')->onDelete('cascade');
            }
            
            // Eliminar columnas antiguas si existen
            if (Schema::hasColumn('asistencias', 'grupo')) {
                $table->dropColumn('grupo');
            }
            if (Schema::hasColumn('asistencias', 'turno')) {
                $table->dropColumn('turno');
            }
            if (Schema::hasColumn('asistencias', 'timestamps')) {
                $table->dropTimestamps();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            // Restaurar columnas antiguas
            $table->string('grupo', 20)->nullable();
            $table->string('turno', 20)->nullable();
            $table->timestamps();
            
            // Eliminar nuevas columnas
            if (Schema::hasColumn('asistencias', 'grupo_id')) {
                $table->dropForeign(['grupo_id']);
                $table->dropColumn('grupo_id');
            }
            if (Schema::hasColumn('asistencias', 'registrado_por')) {
                $table->dropForeign(['registrado_por']);
                $table->dropColumn('registrado_por');
            }
            if (Schema::hasColumn('asistencias', 'estado')) {
                $table->dropColumn('estado');
            }
        });
    }
};
