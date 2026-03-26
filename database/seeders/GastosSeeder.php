<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class GastosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('es_CO');

        $buses = DB::table('bus')->get();
        if ($buses->isEmpty()) return;

        $conceptos = ['Combustible', 'Llantas', 'Peajes', 'Lavado', 'Insumos limpieza'];

        foreach ($buses as $bus) {
            for ($i = 0; $i < rand(5, 10); $i++) {
                DB::table('gastos')->insert([
                    'placa' => $bus->placa,
                    'fecha' => $faker->dateTimeBetween('-2 months', 'now'),
                    'valor' => $faker->randomFloat(2, 5000, 300000),
                    'tipo_gasto' => $faker->randomElement($conceptos),
                    'descripcion' => 'Gasto de mantenimiento rutinario',
                ]);
            }
        }
    }
}
