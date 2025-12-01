<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Alumno;
use App\Models\ContactoAlumno;
use App\Models\Salud;

class AlumnoSeeder extends Seeder
{
    public function run(): void
    {
        // Verificar si ya existen alumnos
        if (Alumno::count() > 0) {
            $this->command->info('Ya existen alumnos en la base de datos.');
            return;
        }

        // Crear alumnos de prueba
        $alumnos = [
            [
                'curp' => 'GOMJ030101HDFLRS01',
                'matricula' => '2024001',
                'nombre_s' => 'Juan',
                'apellido_paterno' => 'Gómez',
                'apellido_materno' => 'Martínez',
                'fecha_nacimiento' => '2009-01-15',
                'sexo' => 'M',
                'generacion' => '2024-2027',
                'grado' => 1,
                'grupo' => 'A',
                'estatus' => 'activo',
            ],
            [
                'curp' => 'LOPM040215MDFLRS02',
                'matricula' => '2024002',
                'nombre_s' => 'María',
                'apellido_paterno' => 'López',
                'apellido_materno' => 'Pérez',
                'fecha_nacimiento' => '2009-02-20',
                'sexo' => 'F',
                'generacion' => '2024-2027',
                'grado' => 1,
                'grupo' => 'A',
                'estatus' => 'activo',
            ],
            [
                'curp' => 'ROSC050310HDFLRS03',
                'matricula' => '2023003',
                'nombre_s' => 'Carlos',
                'apellido_paterno' => 'Rodríguez',
                'apellido_materno' => 'Sánchez',
                'fecha_nacimiento' => '2008-03-10',
                'sexo' => 'M',
                'generacion' => '2023-2026',
                'grado' => 2,
                'grupo' => 'B',
                'estatus' => 'activo',
            ],
        ];

        foreach ($alumnos as $data) {
            $alumno = Alumno::create($data);

            // Crear registro de salud
            Salud::create([
                'alumno_id' => $alumno->id,
                'tipo_sangre' => ['O+', 'A+', 'B+', 'AB+', 'O-', 'A-'][rand(0, 5)],
                'alergias' => rand(0, 1) ? 'Ninguna' : 'Polen, Polvo',
            ]);

            // Crear contacto principal (tutor)
            ContactoAlumno::create([
                'alumno_id' => $alumno->id,
                'nombre_s' => 'Padre/Madre',
                'apellido_paterno' => $data['apellido_paterno'],
                'apellido_materno' => $data['apellido_materno'],
                'parentesco' => 'Padre/Madre',
                'telefono' => '55' . rand(1000, 9999) . rand(1000, 9999),
                'correo' => strtolower($data['apellido_paterno']) . '@example.com',
                'autorizado_recoger' => true,
                'es_principal' => true,
            ]);
        }

        $this->command->info('Se crearon ' . count($alumnos) . ' alumnos de prueba.');
    }
}
