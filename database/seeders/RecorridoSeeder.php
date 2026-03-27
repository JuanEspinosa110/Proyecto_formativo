<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RecorridoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Obtener viajes finalizados
        $viajes = DB::table('viaje')->where('id_estado', 5)->get();

        if ($viajes->isEmpty()) {
            return;
        }

        foreach ($viajes as $v) {
            // Generar 1 recorrido físico para cada registro de viaje header
            DB::table('recorridos')->insert([
                'id_viaje' => $v->id_viaje,
                'sentido' => (rand(0, 1) == 0 ? 'IDA' : 'VUELTA'),
                'hora_salida' => Carbon::parse($v->fecha),
                'hora_llegada' => Carbon::parse($v->fecha)->addMinutes(rand(45, 90)),
                'foto_torniquete' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
