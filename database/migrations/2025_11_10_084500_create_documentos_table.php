<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entidad_id');
            $table->string('entidad_tipo', 50);
            $table->enum('tipo_documento', ['Credencial', 'Autorización_Recogida', 'Pase_Salida', 'Reglamento_Firmado']);
            $table->string('ruta_archivo', 255);
            $table->text('firma')->nullable();
            $table->unsignedBigInteger('creado_por_id');
            $table->timestamp('creado_en')->useCurrent();
            
            $table->foreign('creado_por_id')->references('id')->on('usuarios')->onDelete('cascade');
            
            $table->index(['entidad_id', 'entidad_tipo']);
            $table->index('tipo_documento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
