<?php

namespace App\Services;

use App\Models\ReporteFalla;
use App\Models\Viaje;
use App\Models\Recorrido;
use App\Models\Bus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReporteFallaService
{
    /**
     * Registra una falla y aplica lógica operativa según el nivel de urgencia.
     * 
     * @param string $placa
     * @param int $docUsuario
     * @param string $descripcion
     * @param string|null $nivelUrgencia (Bajo, Medio, Alto)
     * @return ReporteFalla
     */
    public function registrarFalla($placa, $docUsuario, $descripcion, $nivelUrgencia = 'Bajo')
    {
        return DB::transaction(function () use ($placa, $docUsuario, $descripcion, $nivelUrgencia) {
            // 1. Crear el reporte de falla
            $reporte = ReporteFalla::create([
                'placa' => $placa,
                'doc_usuario' => $docUsuario,
                'descripcion' => $descripcion,
                'nivel_urgencia' => $nivelUrgencia ?? 'Bajo',
                'id_estado' => 6 // PENDIENTE
            ]);

            // 2. Si el nivel es ALTO, aplicamos lógica de suspensión operativa
            if ($nivelUrgencia === 'Alto') {
                $this->suspenderOperacion($placa);
            }

            return $reporte;
        });
    }

    /**
     * Finaliza los viajes/recorridos activos del bus y lo pone en mantenimiento.
     */
    protected function suspenderOperacion($placa)
    {
        // A. Poner el Bus en mantenimiento (ID 4)
        $bus = Bus::where('placa', $placa)->first();
        if ($bus) {
            $bus->id_estado = 4; // EN MANTENIMIENTO
            $bus->save();
        }

        // B. Buscar viajes activos del bus hoy
        $viajesActivos = Viaje::where('placa', $placa)
            ->whereIn('id_estado', [1, 4]) // PROGRAMADO o EN CURSO
            ->get();

        foreach ($viajesActivos as $viaje) {
            $viaje->id_estado = 5; // FINALIZADO
            $viaje->save();

            // C. Finalizar recorridos en curso para estos viajes
            Recorrido::where('id_viaje', $viaje->id_viaje)
                ->whereNull('hora_llegada')
                ->update(['hora_llegada' => Carbon::now()]);
        }
    }
}
