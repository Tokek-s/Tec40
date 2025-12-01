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
        Schema::table('usuarios', function (Blueprint $table) {
            // Cambiar la columna rol a ENUM con los valores correctos
            \DB::statement("ALTER TABLE usuarios MODIFY COLUMN rol ENUM('Direccion','Subdireccion','Administrativo','Prefecto','Sistemas','Medico','Psicologo') NOT NULL DEFAULT 'Administrativo'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            // Revertir a string
            $table->string('rol')->default('Sistemas')->change();
        });
    }
};
