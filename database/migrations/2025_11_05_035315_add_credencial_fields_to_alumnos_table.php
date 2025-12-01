<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            // Agregar columnas solo si no existen
            if (!Schema::hasColumn('alumnos', 'grado')) {
                $table->integer('grado')->nullable()->after('generacion');
            }
            
            if (!Schema::hasColumn('alumnos', 'grupo')) {
                $table->string('grupo', 2)->nullable()->after('grado');
            }
        });

        Schema::table('contactos_alumno', function (Blueprint $table) {
            if (!Schema::hasColumn('contactos_alumno', 'es_principal')) {
                $table->boolean('es_principal')->default(false)->after('autorizado_recoger');
            }
        });
    }

    public function down(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $columns = ['grado', 'grupo'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('alumnos', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('contactos_alumno', function (Blueprint $table) {
            if (Schema::hasColumn('contactos_alumno', 'es_principal')) {
                $table->dropColumn('es_principal');
            }
        });
    }
};
