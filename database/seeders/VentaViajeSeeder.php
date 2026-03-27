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
        // Obtener todos los viajes
        $viajes = DB::table('viaje')->get();
        // Obtener todas las tarjetas de los pasajeros
        $tarjetas = DB::table('tarjeta')->get();

        if ($viajes->isEmpty() || $tarjetas->isEmpty()) {
            return;
        }

        $ventas = [];
        $maxId = DB::table('venta_viaje')->max('id_venta') ?? 0;
        $id_venta = $maxId + 1;

        foreach ($viajes as $viaje) {
            // Cantidad aleatoria de pasajeros en el viaje (ej: 5 a 15)
            $numPasajeros = rand(5, 15);

            for ($i = 0; $i < $numPasajeros; $i++) {
                $tarjeta = $tarjetas->random();
                
                // Simular que algunos pasajes se cobran un poco después del inicio del viaje
                $fechaVenta = Carbon::parse($viaje->fecha)->addMinutes(rand(1, 45));

                $ventas[] = [
                    'id_venta' => $id_venta++,
                    'id_viaje' => $viaje->id_viaje,
                    'id_tarjeta' => $tarjeta->id_tarjeta,
                    'valor' => 3300.00, // Precio fijo del pasaje de ejemplo
                    'fecha' => $fechaVenta->format('Y-m-d H:i:s'),
                    'id_estado' => 1, // Finalizado/Exitoso
                ];
                
                // Insertar en bloques para no saturar memoria si hay muchos
                if (count($ventas) > 500) {
                    DB::table('venta_viaje')->insert($ventas);
                    $ventas = [];
                }
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
