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
            // Un 5% de probabilidad de tener un mantenimiento en curso
            $enMantenimiento = rand(1, 20) === 1;

            if ($enMantenimiento) {
                $fechaInicio = Carbon::now()->subDays(rand(1, 4));
                $mantenimientoId = DB::table('mantenimiento')->insertGetId([
                    'placa' => $bus->placa,
                    'NIT' => $bus->NIT,
                    'kilometraje' => $bus->kilometraje,
                    'fecha_mantenimiento' => $fechaInicio,
                    'fecha_proximo' => null,
                    'km_proximo' => null,
                    'costo_total' => 0,
                    'id_estado' => 4, // EN MANTENIMIENTO
                ]);

                // Intentar vincular una falla pendiente
                $fallaPool = DB::table('reportes_fallas')
                    ->where('placa', $bus->placa)
                    ->where('id_estado', 6) // PENDIENTE
                    ->first();

                $descripcionDetalle = 'Mantenimiento preventivo de rutina.';
                $idReporte = null;

                if ($fallaPool) {
                    $idReporte = $fallaPool->id_reporte;
                    $descripcionDetalle = 'ATENCIÓN DE FALLA: ' . $fallaPool->descripcion;
                    // Actualizar falla a "En proceso" (1)
                    DB::table('reportes_fallas')->where('id_reporte', $idReporte)->update(['id_estado' => 1]);
                }

                DB::table('detalle_mantenimiento')->insert([
                    'id_mantenimiento' => $mantenimientoId,
                    'id_tipo_mantenimiento' => $idReporte ? 2 : 1, // 2: Correctivo si hay falla, 1: Preventivo
                    'descripcion' => $descripcionDetalle,
                    'id_reporte' => $idReporte,
                ]);

                // Actualizar el estado del bus a 4 (EN MANTENIMIENTO)
                DB::table('bus')->where('placa', $bus->placa)->update(['id_estado' => 4]);
                
                continue; 
            }

            // 2 a 4 mantenimientos previos finalizados por bus
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

                // Intentar vincular fallas del pasado para estos mantenimientos finalizados
                $fallasHistoricas = DB::table('reportes_fallas')
                    ->where('placa', $bus->placa)
                    ->whereIn('id_estado', [6, 1, 5]) 
                    ->where('created_at', '<=', $fecha)
                    ->limit(rand(1, 2))
                    ->get();

                if ($fallasHistoricas->isNotEmpty()) {
                    foreach ($fallasHistoricas as $falla) {
                        DB::table('detalle_mantenimiento')->insert([
                            'id_mantenimiento' => $mantenimientoId,
                            'id_tipo_mantenimiento' => 2, // Correctivo
                            'descripcion' => 'RESOLUCIÓN DE FALLA: ' . $falla->descripcion,
                            'id_reporte' => $falla->id_reporte,
                        ]);
                        // Sincronizar estado de la falla a FINALIZADO (5)
                        DB::table('reportes_fallas')->where('id_reporte', $falla->id_reporte)->update(['id_estado' => 5]);
                    }
                } else {
                    $descripcionesGenericas = [
                        'Cambio de aceite y filtros de rutina.',
                        'Revisión general del sistema de frenos.',
                        'Mantenimiento preventivo general.',
                        'Ajuste de suspensión y amortiguadores.'
                    ];
                    DB::table('detalle_mantenimiento')->insert([
                        'id_mantenimiento' => $mantenimientoId,
                        'id_tipo_mantenimiento' => 1, 
                        'descripcion' => $faker->randomElement($descripcionesGenericas),
                    ]);
                }
            }
        }
    }
}
