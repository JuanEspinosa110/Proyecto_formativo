<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BarrioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barrios = [
            ['id_barrio' => 1, 'nombre' => 'AUGUSTO E MEDINA', 'id_ciudad' => '730001', 'latitud' => 4.45380950, 'longitud' => -75.24649900],
            ['id_barrio' => 2, 'nombre' => 'BALTAZAR', 'id_ciudad' => '730001', 'latitud' => 4.44264030, 'longitud' => -75.24459210],
            ['id_barrio' => 3, 'nombre' => 'CENTRO', 'id_ciudad' => '730001', 'latitud' => 4.44199350, 'longitud' => -75.23664160],
            ['id_barrio' => 4, 'nombre' => 'COMBEIMA', 'id_ciudad' => '730001', 'latitud' => 4.44113410, 'longitud' => -75.24378280],
            ['id_barrio' => 5, 'nombre' => 'ESTACION', 'id_ciudad' => '730001', 'latitud' => 4.43915650, 'longitud' => -75.23061660],
            ['id_barrio' => 6, 'nombre' => 'INTERLAKEN', 'id_ciudad' => '730001', 'latitud' => 4.44445340, 'longitud' => -75.23422520],
            ['id_barrio' => 7, 'nombre' => 'LA POLA', 'id_ciudad' => '730001', 'latitud' => 4.44715980, 'longitud' => -75.24495430],
            ['id_barrio' => 8, 'nombre' => 'LIBERTADOR', 'id_ciudad' => '730001', 'latitud' => 4.44693020, 'longitud' => -75.24765570],
            ['id_barrio' => 9, 'nombre' => 'POLA PARTE ALTA', 'id_ciudad' => '730001', 'latitud' => 4.44684580, 'longitud' => -75.24421920],
            ['id_barrio' => 10, 'nombre' => 'PUEBLO NUEVO', 'id_ciudad' => '730001', 'latitud' => 4.44608060, 'longitud' => -75.23771150],
            ['id_barrio' => 11, 'nombre' => 'SAN PEDRO ALEJANDRINO', 'id_ciudad' => '730001', 'latitud' => 4.43558820, 'longitud' => -75.23152240],
            ['id_barrio' => 12, 'nombre' => '20 DE JULIO', 'id_ciudad' => '730001', 'latitud' => 4.45378170, 'longitud' => -75.23970710],
            ['id_barrio' => 13, 'nombre' => '7 DE AGOSTO', 'id_ciudad' => '730001', 'latitud' => 4.45343000, 'longitud' => -75.23642670],
            ['id_barrio' => 14, 'nombre' => 'ALASKA', 'id_ciudad' => '730001', 'latitud' => 4.45642520, 'longitud' => -75.24602680],
            ['id_barrio' => 15, 'nombre' => 'ANCON', 'id_ciudad' => '730001', 'latitud' => 4.45168750, 'longitud' => -75.23461030],
            ['id_barrio' => 16, 'nombre' => 'BELEN', 'id_ciudad' => '730001', 'latitud' => 4.45114510, 'longitud' => -75.24305630],
            ['id_barrio' => 17, 'nombre' => 'BELENCITO', 'id_ciudad' => '730001', 'latitud' => 4.45034440, 'longitud' => -75.23847310],
            ['id_barrio' => 18, 'nombre' => 'BELENCITO', 'id_ciudad' => '730001', 'latitud' => 4.45034440, 'longitud' => -75.23847310],
            ['id_barrio' => 19, 'nombre' => 'CENTENARIO', 'id_ciudad' => '730001', 'latitud' => 4.44760970, 'longitud' => -75.23909130],
            ['id_barrio' => 20, 'nombre' => 'CERRO PAN DE AZUCAR', 'id_ciudad' => '730001', 'latitud' => 4.43890000, 'longitud' => -75.23220000],
            // ...más barrios...
        ];
        \Illuminate\Support\Facades\DB::table('barrio')->insertOrIgnore($barrios);
    }
}
