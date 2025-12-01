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
        Schema::table('alumnos', function (Blueprint $table) {
            // Primero eliminar la foreign key
            $table->dropForeign('fk_alumnos_grupo');
            // Luego eliminar la columna
            $table->dropColumn('grupo_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->unsignedBigInteger('grupo_id')->nullable();
            $table->foreign('grupo_id', 'fk_alumnos_grupo')->references('id')->on('grupos');
        });
    }
};
