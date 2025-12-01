<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            if (!Schema::hasColumn('asistencias', 'grupo')) {
                $table->string('grupo', 20)->nullable()->after('fecha');
            }
            if (!Schema::hasColumn('asistencias', 'turno')) {
                $table->string('turno', 20)->nullable()->after('grupo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('asistencias', function (Blueprint $table) {
            if (Schema::hasColumn('asistencias', 'turno')) {
                $table->dropColumn('turno');
            }
            if (Schema::hasColumn('asistencias', 'grupo')) {
                $table->dropColumn('grupo');
            }
        });
    }
};
