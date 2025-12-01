<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class FechaCuestionarioSeeder extends Seeder
{
    public function run(): void
    {
        $hoy = now()->toDateString();
        $fin = now()->addDays(10)->toDateString();

        DB::table('fechas_cuestionarios')->insert([
            [
                'titulo' => 'Evaluación Inicial',
                'descripcion' => 'Cuestionario general de evaluación para todos los grados.',
                'fecha_inicio' => $hoy,
                'fecha_fin' => $fin,
                'activo' => 1,
                'primero_activo' => 1,
                'segundo_activo' => 1,
                'tercero_activo' => 0,
            ],
        ]);
    }
}
