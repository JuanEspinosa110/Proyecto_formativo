<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class PasajeroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('es_CO');

        for ($i = 0; $i < 10; $i++) {
            $doc_usuario = $faker->unique()->numberBetween(1000000000, 1999999999);
            $id_tarjeta = $faker->unique()->numerify('##########');

            // 1. Crear Usuario (Pasajero)
            DB::table('usuario')->insert([
                'doc_usuario' => $doc_usuario,
                'primer_nombre' => strtoupper($faker->firstName),
                'segundo_nombre' => strtoupper($faker->firstName),
                'primer_apellido' => strtoupper($faker->lastName),
                'segundo_apellido' => strtoupper($faker->lastName),
                'correo' => $faker->unique()->safeEmail,
                'password' => Hash::make('pasajero123*'),
                'telefono' => '3' . $faker->numberBetween(0, 5) . $faker->numberBetween(10000000, 99999999),
                'id_tipo_usuario' => 2, // Pasajero
                'id_ciudad' => '730001', // Ibagué
                'id_estado' => 1, // Activo
            ]);

            // 2. Crear Tarjeta
            DB::table('tarjeta')->insert([
                'id_tarjeta' => $id_tarjeta,
                'doc_usuario' => $doc_usuario,
                'saldo' => 0, // El saldo real se calcula al final con recargas y pasajes
                'codigo_tarjeta' => $faker->unique()->numerify('###############'),
                'id_estado' => 1, // Activo
            ]);

            // 3. Crear Titularidad de Tarjeta
            DB::table('titularidad_tarjeta')->insert([
                'id_tarjeta' => $id_tarjeta,
                'doc_usuario' => $doc_usuario,
                'fecha_inicio' => now()->format('Y-m-d'),
                'id_estado' => 1, // Activo
            ]);
        }

        // 4. Crear Tarjetas sin asociar (Estado 2, doc_usuario = null, sin titularidad)
        for ($i = 0; $i < 10; $i++) {
            $id_tarjeta_nueva = $faker->unique()->numerify('##########');

            DB::table('tarjeta')->insert([
                'id_tarjeta' => $id_tarjeta_nueva,
                'doc_usuario' => null,
                'saldo' => 0,
                'codigo_tarjeta' => $faker->unique()->numerify('###############'),
                'id_estado' => 2, // Inactiva / Lista para ser asignada
            ]);
        }
    }
}
