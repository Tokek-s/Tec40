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
        Schema::table('incidencias', function (Blueprint $table) {
            $table->string('nombre_docente_reporta')->nullable()->after('firma_docente');
            $table->string('nombre_tutor_firma')->nullable()->after('firma_tutor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidencias', function (Blueprint $table) {
            $table->dropColumn(['nombre_docente_reporta', 'nombre_tutor_firma']);
        });
    }
};
