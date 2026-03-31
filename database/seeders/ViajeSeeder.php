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
        $asignacionesTotales = DB::table('asignacion')
            ->where('id_estado', 1)
            ->get();

        if ($asignacionesTotales->isEmpty()) {
            $this->command->warn('No hay asignaciones activas para generar viajes.');
            return;
        }

        // Dejar un 20% de los buses/asignaciones sin viajes (Requerimiento)
        $cantidadAProcesar = (int) (count($asignacionesTotales) * 0.8);
        $asignaciones = $asignacionesTotales->shuffle()->take($cantidadAProcesar);

        if ($asignaciones->isEmpty()) {
            $this->command->warn('No hay asignaciones seleccionadas para generar viajes.');
            return;
        }

        $viajes = [];
        $maxId = DB::table('viaje')->max('id_viaje') ?? 0;
        $id_viaje = $maxId + 1;

        // 2. Generar un solo viaje por conductor para los últimos 7 días
        $minutosOpciones = [0, 15, 30];

        for ($dia = 0; $dia < 7; $dia++) {
            $fechaBase = now()->subDays($dia)->startOfDay();

            foreach ($asignaciones as $asig) {
                // Solo un viaje por conductor al día
                $horaInicio = rand(5, 15);
                $minutoInicio = $minutosOpciones[array_rand($minutosOpciones)];
                
                $tiempoViaje = $fechaBase->copy()->addHours($horaInicio)->addMinutes($minutoInicio);

                $viajes[] = [
                    'id_viaje' => $id_viaje++,
                    'placa' => $asig->placa,
                    'id_ruta' => $asig->id_ruta,
                    'doc_us' => $asig->doc_usuario,
                    'fecha' => $tiempoViaje->format('Y-m-d H:i:s'),
                    'fecha_asignacion' => $tiempoViaje->format('Y-m-d H:i:s'),
                    'id_estado' => 5, // FINALIZADO
                ];

                // Insertar en bloques para no saturar memoria
                if (count($viajes) >= 500) {
                    DB::table('viaje')->insert($viajes);
                    $viajes = [];
                }
            }
        }

        // 3. Generar viajes programados para el 5, 6 y 7 de abril (Estado 1: ACTIVO)
        $diasAbril = [5, 6, 7];
        foreach ($diasAbril as $dia) {
            // Asumiendo el año 2026 basado en la fecha actual del sistema
            $fechaAbril = Carbon::create(2026, 4, $dia, 0, 0, 0);

            foreach ($asignaciones as $asig) {
                $horaInicio = rand(6, 18);
                $minutoInicio = [0, 15, 30, 45][array_rand([0, 15, 30, 45])];
                $tiempoViaje = $fechaAbril->copy()->addHours($horaInicio)->addMinutes($minutoInicio);

                $viajes[] = [
                    'id_viaje' => $id_viaje++,
                    'placa' => $asig->placa,
                    'id_ruta' => $asig->id_ruta,
                    'doc_us' => $asig->doc_usuario,
                    'fecha' => $tiempoViaje->format('Y-m-d H:i:s'),
                    'fecha_asignacion' => $tiempoViaje->format('Y-m-d H:i:s'),
                    'id_estado' => 1, // ACTIVO
                ];

                if (count($viajes) >= 500) {
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
