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
            // Generar 1 recorrido hoy para cada asignación
            DB::table('recorridos')->insert([
                'placa' => $asig->placa,
                'id_ruta' => $asig->id_ruta,
                'doc_us' => $asig->doc_usuario,
                'hora_salida' => now()->subHours(2),
                'hora_llegada' => now(),
                'ingresos' => rand(15000, 50000),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
