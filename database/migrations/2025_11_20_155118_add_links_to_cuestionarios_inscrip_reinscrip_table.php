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
            $table->text('link_primero')->nullable()->after('descripcion');
            $table->text('link_segundo')->nullable()->after('link_primero');
            $table->text('link_tercero')->nullable()->after('link_segundo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cuestionarios_inscrip_reinscrip', function (Blueprint $table) {
            $table->dropColumn(['link_primero', 'link_segundo', 'link_tercero']);
        });
    }
};
