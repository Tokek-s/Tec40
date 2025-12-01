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
            $table->string('firma_docente')->nullable()->after('fecha');
            $table->string('firma_tutor')->nullable()->after('firma_docente');
            $table->string('pdf_path')->nullable()->after('firma_tutor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidencias', function (Blueprint $table) {
            $table->dropColumn(['firma_docente', 'firma_tutor', 'pdf_path']);
        });
    }
};
