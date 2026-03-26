<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ReporteFallasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('es_CO');

        // Conductores disponibles
        $conductores = DB::table('usuario')->where('id_tipo_usuario', 3)->get();
        $buses = DB::table('bus')->get();

        if ($conductores->isEmpty() || $buses->isEmpty()) return;

        $descripciones = [
            'Falla en luces traseras',
            'Aire acondicionado no enfría',
            'Ruidos en el motor',
            'Frenos largos',
            'Puerta trasera trabada',
            'Limpiaparabrisas defectuoso',
            'Batería con bajo voltaje',
            'Fuga de aceite detectada',
        ];

        for ($i = 0; $i < 20; $i++) {
            $conductor = $conductores->random();
            $bus = $buses->where('NIT', $conductor->NIT)->first() ?: $buses->random();

            DB::table('reportes_fallas')->insert([
                'doc_usuario' => $conductor->doc_usuario,
                'placa' => $bus->placa,
                'created_at' => $faker->dateTimeBetween('-1 month', 'now'),
                'descripcion' => $faker->randomElement($descripciones),
                'nivel_urgencia' => $faker->randomElement(['Bajo', 'Medio', 'Alto']),
                'id_estado' => $faker->randomElement([6, 1]), // PENDIENTE o ACTIVO (atendido)
            ]);
        }
    }
}
