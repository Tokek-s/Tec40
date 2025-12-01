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
        Schema::table('cuestionarios_inscrip_reinscrip', function (Blueprint $table) {
            $table->enum('tipo', ['inscripcion', 'reinscripcion'])->default('inscripcion')->after('tercero_activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cuestionarios_inscrip_reinscrip', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
    }
};
