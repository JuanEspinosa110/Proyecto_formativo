<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RecorridoSeeder extends Seeder
{
    public function run(): void
    {
        $asignaciones = DB::table('asignacion')->get();
        if ($asignaciones->isEmpty()) return;

        foreach ($asignaciones as $asig) {
            // Obtener un viaje para esta asignación (o crear uno si es necesario para el seeder)
            $viaje = DB::table('viaje')
                ->where('placa', $asig->placa)
                ->where('doc_us', $asig->doc_usuario)
                ->first();

            if ($viaje) {
                // Generar 1 recorrido hoy para cada asignación
                DB::table('recorridos')->insert([
                    'id_viaje' => $viaje->id_viaje,
                    'sentido' => 'IDA',
                    'hora_salida' => now()->subHours(2),
                    'hora_llegada' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
