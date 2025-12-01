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
        Schema::table('anuncios', function (Blueprint $table) {
            $table->unsignedBigInteger('archivo_multimedia_id')->nullable()->after('ruta_imagen');
            $table->foreign('archivo_multimedia_id')->references('id')->on('archivos_multimedia')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anuncios', function (Blueprint $table) {
            $table->dropForeign(['archivo_multimedia_id']);
            $table->dropColumn('archivo_multimedia_id');
        });
    }
};
