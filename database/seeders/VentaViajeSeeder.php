<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VentaViajeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener solo los IDs de los viajes que tienen recorridos reales registrados
        $viajesIdsConRecorridos = DB::table('recorridos')->pluck('id_viaje')->unique();

        // Obtener solo los viajes que SÍ tienen recorridos y que el estado sea finalizado o en curso
        $viajes = DB::table('viaje')->whereIn('id_viaje', $viajesIdsConRecorridos)->get();
        // Obtener todas las tarjetas de los pasajeros
        $tarjetas = DB::table('tarjeta')->get();

        if ($viajes->isEmpty() || $tarjetas->isEmpty()) {
            return;
        }

        $ventas = [];
        $maxId = DB::table('venta_viaje')->max('id_venta') ?? 0;
        $id_venta = $maxId + 1;

        // Cargar todos los recorridos en memoria (agrupados por viaje) para distribuir ventas
        $recorridosTodos = DB::table('recorridos')->get()->groupBy('id_viaje');

        foreach ($viajes as $viaje) {
            $recorridos = $recorridosTodos->get($viaje->id_viaje);
            
            if (!$recorridos) continue;

            foreach ($recorridos as $recorrido) {
                // Cantidad aleatoria de pasajeros en ESTE recorrido en específico (ej: 5 a 15)
                $numPasajeros = rand(5, 15);

                $horaSalida = Carbon::parse($recorrido->hora_salida);
                $horaLlegada = $recorrido->hora_llegada ? Carbon::parse($recorrido->hora_llegada) : $horaSalida->copy()->addMinutes(45);

                // Calcular la diferencia en minutos entre salida y llegada para distribuir ventas
                $diffMinutes = $horaLlegada->diffInMinutes($horaSalida);
                if ($diffMinutes < 1) $diffMinutes = 1;

                for ($i = 0; $i < $numPasajeros; $i++) {
                    $tarjeta = $tarjetas->random();
                    
                    // Simular que el pasaje se cobra en algún momento durante este recorrido exacto
                    $fechaVenta = $horaSalida->copy()->addMinutes(rand(0, $diffMinutes));

                    $ventas[] = [
                        'id_venta' => $id_venta++,
                        'id_viaje' => $viaje->id_viaje,
                        'id_tarjeta' => $tarjeta->id_tarjeta,
                        'valor' => 3300.00, // Precio fijo del pasaje
                        'fecha' => $fechaVenta->format('Y-m-d H:i:s'),
                        'id_estado' => 1, // Finalizado/Exitoso
                    ];
                    
                    if (count($ventas) > 500) {
                        DB::table('venta_viaje')->insert($ventas);
                        $ventas = [];
                    }
                }
            }
                
            // Insertar en bloques para no saturar memoria si hay muchos
            if (count($ventas) > 500) {
                DB::table('venta_viaje')->insert($ventas);
                $ventas = [];
            }
        }

        // Insertar restantes
        if (count($ventas) > 0) {
            DB::table('venta_viaje')->insert($ventas);
        }

        // --- Ajustar matemáticamente los saldos ---
        // Cuadrar el saldo de todas las tarjetas basado en: Recargas - Ventas de Viaje
        $gestor = DB::table('usuario')->where('id_tipo_usuario', 8)->first();
        $doc_gestor = $gestor ? $gestor->doc_usuario : null;

        $todasLasTarjetas = DB::table('tarjeta')->get();
        foreach ($todasLasTarjetas as $tarjeta) {
            $totalRecargas = DB::table('recarga')->where('id_tarjeta', $tarjeta->id_tarjeta)->sum('monto');
            $totalVentas = DB::table('venta_viaje')->where('id_tarjeta', $tarjeta->id_tarjeta)->sum('valor');
            
            $saldoReal = $totalRecargas - $totalVentas;
            
            // Si la tarjeta quedó con saldo negativo por viajar más de lo que recargó (asumiendo generación aleatoria):
            // Le insertamos una recarga adicional redondeada (múltiplo de 1000) para que el saldo quede positivo y cuadrado.
            if ($saldoReal < 0) {
                // Recargar el faltante exacto más un excedente aleatorio redondo.
                $montoExtra = ceil((abs($saldoReal) + rand(2000, 10000)) / 1000) * 1000;
                
                DB::table('recarga')->insert([
                    'id_tarjeta' => $tarjeta->id_tarjeta,
                    'doc_usuario_gestor' => $doc_gestor,
                    'monto' => $montoExtra,
                    'created_at' => Carbon::now(),
                ]);
                $saldoReal += $montoExtra;
            }
            
            // Actualizar la tabla tarjeta con su saldo final exacto (con importes redondos desde 100)
            DB::table('tarjeta')->where('id_tarjeta', $tarjeta->id_tarjeta)->update([
                'saldo' => $saldoReal
            ]);
        }
    }
}
