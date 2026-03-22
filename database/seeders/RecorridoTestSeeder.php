<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RecorridoTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Obtener al menos un Bus Activo, Conductor y Ruta
        $bus = DB::table('bus')->where('id_estado', 1)->first() ?? DB::table('bus')->first();
        $conductor = DB::table('usuario')->where('id_tipo_usuario', 3)->first();
        $ruta = DB::table('ruta')->first();

        if (!$bus || !$conductor || !$ruta) {
            $this->command->warn('No se encontraron datos básicos. Asegúrate de tener al menos un Bus, Conductor y Ruta creados.');
            return;
        }

        // 2. Crear una asignación (Viaje) simulada del día de hoy
        $id_viaje = rand(100000, 999999);
        DB::table('viaje')->insert([
            'id_viaje' => $id_viaje,
            'placa' => $bus->placa,
            'id_ruta' => $ruta->id_ruta,
            'doc_us' => $conductor->doc_usuario,
            'fecha' => Carbon::today()->setTime(6, 0)->toDateTimeString(), // Inicio de turno 6:00 AM
            'id_estado' => 12 // 12 = EN CURSO
        ]);

        // 3. Simular Pasajeros usando Tarjetas (Tabla venta_viaje)
        $tarjeta = DB::table('tarjeta')->first();
        if ($tarjeta) {
            $this->command->info('Generando pasajeros simulados...');
            for ($i = 0; $i < 45; $i++) {
                // Simulamos 45 personas que pasaron en diferentes horas
                DB::table('venta_viaje')->insert([
                    'id_viaje' => $id_viaje,
                    'id_tarjeta' => $tarjeta->id_tarjeta,
                    'valor' => 3300,
                    'fecha' => Carbon::today()->setTime(6, 30)->addMinutes($i * 5)->toDateTimeString(),
                    'id_estado' => 18, // 18 = PAGADO
                ]);
            }
        }
        else {
            $this->command->warn('No hay tarjetas disponibles (tabla tarjeta). Se omite simulacion de pasajeros.');
        }

        // 4. Crear el Recorrido de "IDA" (Finalizado con Foto)
        DB::table('recorridos')->insert([
            'id_viaje' => $id_viaje,
            'sentido' => 'IDA',
            'hora_salida' => Carbon::today()->setTime(6, 0)->toDateTimeString(),
            'hora_llegada' => Carbon::today()->setTime(7, 30)->toDateTimeString(),
            'foto_torniquete' => null // Ojo, sin foto de stock real
        ]);

        // 5. Crear el Recorrido de "VUELTA" (Aún Activo, En Ruta)
        DB::table('recorridos')->insert([
            'id_viaje' => $id_viaje,
            'sentido' => 'VUELTA',
            'hora_salida' => Carbon::today()->setTime(8, 0)->toDateTimeString(),
            'hora_llegada' => null,
            'foto_torniquete' => null
        ]);

        $this->command->info('Seeder de pruebas Completado: Se han generado datos de Viajes, Pasajeros y Recorridos (IDA/VUELTA).');
    }
}
