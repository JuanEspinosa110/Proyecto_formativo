<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class RecargaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('es_CO');

        // 1. Obtener todas las tarjetas creadas (de PasajeroSeeder)
        $tarjetas = DB::table('tarjeta')->pluck('id_tarjeta');

        if ($tarjetas->isEmpty()) {
            $this->command->warn('No se encontraron tarjetas. Asegúrese de ejecutar PasajeroSeeder primero.');
            return;
        }

        // 2. Obtener un Gestor de Recargas (id_tipo_usuario = 8)
        $gestor = DB::table('usuario')
            ->where('id_tipo_usuario', 8)
            ->first();

        if (!$gestor) {
            $this->command->warn('No se encontró un Gestor de Recargas. Asegúrese de ejecutar UsuarioEmpresaSeeder primero.');
            return;
        }

        // 3. Crear 3 a 5 recargas por cada tarjeta
        foreach ($tarjetas as $id_tarjeta) {
            $numRecargas = rand(3, 5);
            
            for ($i = 0; $i < $numRecargas; $i++) {
                $monto = $faker->randomElement([5000, 10000, 20000, 50000]);
                
                DB::table('recarga')->insert([
                    'id_tarjeta' => $id_tarjeta,
                    'doc_usuario_gestor' => is_object($gestor) ? $gestor->doc_usuario : null,
                    'monto' => $monto,
                    'created_at' => $faker->dateTimeBetween('-1 month', 'now'),
                ]);
            }
        }

        $this->command->info('RecargaSeeder ejecutado con éxito.');
    }
}
