<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestRutaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear algunas Rutas de prueba para Ibagué (id_ciudad = 730001)
        // Usaremos barrios distantes para que se vea una ruta larga en el mapa.
        
        $rutas = [
            [
                // Ruta Centro - El Salado
                'id_ruta' => 101,
                'codigo_ruta' => '82',
                'id_ciudad' => '730001',
                'id_barrio_origen' => 3,    // CENTRO
                'id_barrio_destino' => 173, // EL SALADO
                'id_estado' => 1
            ],
            [
                // Ruta Boqueron - Picaleña
                'id_ruta' => 102,
                'codigo_ruta' => '40',
                'id_ciudad' => '730001',
                'id_barrio_origen' => 431,  // BOQUERON
                'id_barrio_destino' => 324, // PICALEDA (Picaleña)
                'id_estado' => 1
            ],
            [
                // Ruta Ricaurte - Pedregal
                'id_ruta' => 103,
                'codigo_ruta' => '17',
                'id_ciudad' => '730001',
                'id_barrio_origen' => 420,  // RICAURTE
                'id_barrio_destino' => 168, // URB. LOS GUALANDAYES (Cerca pedregal/ambalá)
                'id_estado' => 1
            ],
            [
                // Ruta Jardín - Sur (Yuldaima)
                'id_ruta' => 104,
                'codigo_ruta' => '8',
                'id_ciudad' => '730001',
                'id_barrio_origen' => 243,  // JARDIN I
                'id_barrio_destino' => 429, // YULDAIMA
                'id_estado' => 1
            ],
            [
                // Ruta Estadio (Macarena) - Belén
                'id_ruta' => 105,
                'codigo_ruta' => '1',
                'id_ciudad' => '730001',
                'id_barrio_origen' => 374,  // MACARENA PARTE ALTA
                'id_barrio_destino' => 16,  // BELEN
                'id_estado' => 1
            ]
        ];

        DB::table('ruta')->upsert($rutas, ['id_ruta'], [
            'codigo_ruta', 'id_ciudad', 'id_barrio_origen', 'id_barrio_destino', 'id_estado'
        ]);
        
        $this->command->info('Rutas de prueba (82, 40, 17, 8, 1) insertadas correctamente en Ibagué.');
    }
}
