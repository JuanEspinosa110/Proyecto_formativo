<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class NovedadRecorridoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('es_CO');

        // Obtener recorridos recientes
        $recorridos = DB::table('recorridos')->get();
        $controlador = DB::table('usuario')->where('id_tipo_usuario', 5)->first(); // Controlador

        if ($recorridos->isEmpty() || !$controlador) return;

        $novedades = [
            'Trancón fuerte por accidente',
            'Desvío por obra en la vía',
            'Retraso por clima lluvioso',
            'Asistencia médica a pasajero',
        ];

        foreach ($recorridos->shuffle()->take(10) as $rec) {
            DB::table('novedad_recorridos')->insert([
                'id_recorrido' => $rec->id_recorrido,
                'doc_controlador' => $controlador->doc_usuario,
                'tipo' => $faker->randomElement(['CHECKPOINT', 'INCIDENCIA']),
                'descripcion' => $faker->randomElement($novedades),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
