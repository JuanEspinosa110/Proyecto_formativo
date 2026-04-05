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
            $minutosAcumulados = 0;
            $metaMinutos = rand(480, 540); // Entre 8 y 9 horas (480min a 540min)
            
            // Inicia 30 min antes o después de la fecha del viaje
            $tiempoActual = Carbon::parse($v->fecha)->addMinutes(rand(-30, 30));
            $sentido = 'IDA';

            while ($minutosAcumulados < $metaMinutos) {
                // Un recorrido dura entre 60 y 90 minutos
                $duracion = rand(60, 90);
                
                // Ajustar si el último tramo excede la meta
                if ($minutosAcumulados + $duracion > $metaMinutos) {
                    $duracion = $metaMinutos - $minutosAcumulados;
                }

                $horaLlegada = $tiempoActual->copy()->addMinutes($duracion);

                DB::table('recorridos')->insert([
                    'id_viaje' => $v->id_viaje,
                    'sentido' => $sentido,
                    'hora_salida' => $tiempoActual,
                    'hora_llegada' => $horaLlegada,
                    'foto_torniquete' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $minutosAcumulados += $duracion;
                
                // El siguiente recorrido inicia después de un descanso de 10-20 min
                $tiempoActual = $horaLlegada->copy()->addMinutes(rand(10, 20));
                
                // Alternar sentido
                $sentido = ($sentido == 'IDA' ? 'VUELTA' : 'IDA');
            }
        }
    }
}
