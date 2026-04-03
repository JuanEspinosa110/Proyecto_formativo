<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class MantenimientoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (DB::table('mantenimiento')->exists()) {
            $this->command->info('MantenimientoSeeder: Los datos ya existen, saltando...');
            return;
        }

        $faker = Faker::create('es_CO');

        $buses = DB::table('bus')->get();
        if ($buses->isEmpty()) return;

        foreach ($buses as $bus) {
            // 2 a 4 mantenimientos por bus
            for ($i = 0; $i < rand(2, 4); $i++) {
                $fecha = $faker->dateTimeBetween('-6 months', '-1 month');
                $km_actual = round(($bus->kilometraje - rand(1000, 5000)) / 100) * 100;
                $km_proximo = round(($bus->kilometraje + 5000) / 100) * 100;
                $costo = round($faker->randomFloat(2, 100000, 2000000) / 1000) * 1000;
                
                $mantenimientoId = DB::table('mantenimiento')->insertGetId([
                    'placa' => $bus->placa,
                    'NIT' => $bus->NIT,
                    'kilometraje' => $km_actual,
                    'fecha_mantenimiento' => $fecha,
                    'fecha_proximo' => Carbon::parse($fecha)->addMonths(6),
                    'km_proximo' => $km_proximo,
                    'costo_total' => $costo,
                    'id_estado' => 5, // FINALIZADO
                ]);

                $descripciones = [
                    'Cambio de aceite y filtros de rutina.',
                    'Revisión general del sistema de frenos y pastillas.',
                    'Cambio de llantas y alineación.',
                    'Reparación del sistema eléctrico y luces.',
                    'Mantenimiento preventivo general del motor.',
                    'Ajuste de suspensión y revisión de amortiguadores.',
                    'Sustitución de piezas desgastadas en la transmisión.',
                    'Revisión y mantenimiento del sistema de enfriamiento.',
                    'Corrección de fugas en el sistema hidráulico.',
                    'Diagnóstico y corrección de fallas en los sensores del vehículo.'
                ];

                DB::table('detalle_mantenimiento')->insert([
                    'id_mantenimiento' => $mantenimientoId,
                    'id_tipo_mantenimiento' => rand(1, 3), // 1: Preventivo, 2: Correctivo, 3: Predictivo
                    'descripcion' => $faker->randomElement($descripciones), // Genera una descripción realista en español
                ]);
            }
        }
    }
}
