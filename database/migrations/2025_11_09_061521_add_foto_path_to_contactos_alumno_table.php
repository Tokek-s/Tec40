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
        Schema::table('contactos_alumno', function (Blueprint $table) {
            $table->string('foto_path', 255)->nullable()->after('autorizado_recoger');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contactos_alumno', function (Blueprint $table) {
            $table->dropColumn('foto_path');
        });
    }
};
