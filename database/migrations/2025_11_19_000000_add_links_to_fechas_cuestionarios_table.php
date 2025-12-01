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
        Schema::table('fechas_cuestionarios', function (Blueprint $table) {
            $table->text('link_primero')->nullable();
            $table->text('link_segundo')->nullable();
            $table->text('link_tercero')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fechas_cuestionarios', function (Blueprint $table) {
            $table->dropColumn(['link_primero', 'link_segundo', 'link_tercero']);
        });
    }
};
