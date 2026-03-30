<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanesLicenciaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $planes = [
            [
                'nombre_plan' => 'Plan Básico (1 Año)',
                'duracion_meses' => 12,
                'precio' => 1200000.00,
                'descripcion' => 'Plan de suscripción de 1 año.',
                'id_estado' => 1,
            ],
            [
                'nombre_plan' => 'Plan Intermedio (1.5 Años)',
                'duracion_meses' => 18,
                'precio' => 1700000.00,
                'descripcion' => 'Plan de suscripción de 1 año y medio.',
                'id_estado' => 1,
            ],
            [
                'nombre_plan' => 'Plan Avanzado (2 Años)',
                'duracion_meses' => 24,
                'precio' => 2200000.00,
                'descripcion' => 'Plan de suscripción de 2 años.',
                'id_estado' => 1,
            ],
            [
                'nombre_plan' => 'Plan Premium (3 Años)',
                'duracion_meses' => 36,
                'precio' => 2700000.00,
                'descripcion' => 'Plan de suscripción de 3 años.',
                'id_estado' => 1,
            ],
        ];

        DB::table('planes_licencia')->insert($planes);
    }
}
