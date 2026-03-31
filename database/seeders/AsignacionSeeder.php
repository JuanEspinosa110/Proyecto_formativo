<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class AsignacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('es_CO');

        // 1. Obtener buses y rutas
        $buses = DB::table('bus')->get();
        $rutas = DB::table('ruta')->pluck('id_ruta');

        if ($buses->isEmpty() || $rutas->isEmpty()) {
            $this->command->warn('No hay buses o rutas suficientes.');
            return;
        }

        // 2. Obtener conductores activos por NIT de empresa
        $conductores = DB::table('usuario')
            ->where('id_tipo_usuario', 3) // Conductor
            ->where('id_estado', 1)      // Activo
            ->get();

        if ($conductores->isEmpty()) {
            $this->command->warn('No hay conductores activos.');
            return;
        }

        // 3. Crear asignaciones
        // Dejar un 10% de los buses sin asignaciones para mayor realismo (Requerimiento)
        $busesAProcesar = $buses->shuffle()->take((int) (count($buses) * 0.9));

        // Para cada bus seleccionado, intentamos asignar un conductor y una ruta
        foreach ($busesAProcesar as $bus) {
            // Filtrar conductores de la misma empresa (NIT) si es posible
            $conductorEmpresa = $conductores->where('NIT', $bus->NIT)->shuffle()->first();
            
            if (!$conductorEmpresa) continue;

            $id_ruta = $rutas->random();

            DB::table('asignacion')->insert([
                'id_tipo_asignacion' => 1, // Permanente/Regular
                'placa' => $bus->placa,
                'doc_usuario' => $conductorEmpresa->doc_usuario,
                'id_ruta' => $id_ruta,
                'fecha_inicio' => now()->subMonths(2),
                'fecha_fin' => now()->addMonths(6),
                'id_estado' => 1, // ACTIVO
                'NIT' => $bus->NIT
            ]);
        }

        $this->command->info('AsignacionSeeder ejecutado con éxito.');
    }
}
