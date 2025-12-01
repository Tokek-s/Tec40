<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertas_sistema', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['sobrecupo', 'error', 'advertencia', 'info'])->default('info');
            $table->text('mensaje');
            $table->string('grado', 50)->nullable();
            $table->string('turno', 50)->nullable();
            $table->timestamp('fecha_creacion');
            $table->boolean('resuelta')->default(0);
            $table->timestamp('fecha_resolucion')->nullable();
            $table->text('nota_resolucion')->nullable();
            $table->timestamps();
            
            $table->index(['resuelta', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas_sistema');
    }
};
