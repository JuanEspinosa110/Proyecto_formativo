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
        $faker = Faker::create('es_CO');

        $buses = DB::table('bus')->get();
        if ($buses->isEmpty()) return;

        foreach ($buses as $bus) {
            // 2 a 4 mantenimientos por bus
            for ($i = 0; $i < rand(2, 4); $i++) {
                $fecha = $faker->dateTimeBetween('-6 months', '-1 month');
                $costo = $faker->randomFloat(2, 100000, 2000000);
                
                DB::table('mantenimiento')->insert([
                    'placa' => $bus->placa,
                    'NIT' => $bus->NIT,
                    'kilometraje' => $bus->kilometraje - rand(1000, 5000),
                    'fecha_mantenimiento' => $fecha,
                    'fecha_proximo' => Carbon::parse($fecha)->addMonths(6),
                    'km_proximo' => $bus->kilometraje + 5000,
                    'costo_total' => $costo,
                    'id_estado' => 5, // FINALIZADO
                ]);
            }
        }
    }
}
