<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ViajeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Obtener asignaciones activas
        $asignaciones = DB::table('asignacion')
            ->where('id_estado', 1)
            ->get();

        if ($asignaciones->isEmpty()) {
            $this->command->warn('No hay asignaciones activas para generar viajes.');
            return;
        }

        $viajes = [];
        $maxId = DB::table('viaje')->max('id_viaje') ?? 0;
        $id_viaje = $maxId + 1;

        // 2. Generar viajes para los últimos 7 días
        for ($dia = 0; $dia < 7; $dia++) {
            $fechaBase = now()->subDays($dia)->startOfDay();

            foreach ($asignaciones as $asig) {
                // Cada conductor tiene un turno de máximo 8 horas
                // Empezamos a una hora aleatoria entre las 4am y las 2pm
                $horaInicio = rand(4, 14);
                $tiempoActual = $fechaBase->copy()->addHours($horaInicio);
                $tiempoFinTurno = $tiempoActual->copy()->addHours(8);

                // Un viaje dura aprox 45 a 90 minutos
                while ($tiempoActual->lessThan($tiempoFinTurno)) {
                    $viajes[] = [
                        'id_viaje' => $id_viaje++,
                        'placa' => $asig->placa,
                        'id_ruta' => $asig->id_ruta,
                        'doc_us' => $asig->doc_usuario,
                        'fecha' => $tiempoActual->format('Y-m-d H:i:s'),
                        'id_estado' => 5, // FINALIZADO
                    ];

                    // Avanzar al siguiente viaje (duración + descanso en terminal)
                    $tiempoActual->addMinutes(rand(60, 120));
                }

                // Insertar en bloques para no saturar memoria si hay muchos
                if (count($viajes) > 500) {
                    DB::table('viaje')->insert($viajes);
                    $viajes = [];
                }
            }
        }

        // Insertar restantes
        if (count($viajes) > 0) {
            DB::table('viaje')->insert($viajes);
        }

        $this->command->info('ViajeSeeder ejecutado con éxito.');
    }
}
