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
        Schema::create('inscripciones_externas', function (Blueprint $table) {
            $table->id();
            $table->string('curp', 18)->unique()->comment('CURP del alumno inscrito/reinscrito');
            $table->timestamps();
            
            // Los campos extra se agregarán dinámicamente
            // Ejemplo: nombre_tutor, telefono_emergencia, etc.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscripciones_externas');
    }
};
