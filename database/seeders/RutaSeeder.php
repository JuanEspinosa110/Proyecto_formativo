<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RutaSeeder extends Seeder
{
    public function run()
    {
        // Rutas principales
        DB::table('ruta')->insertOrIgnore([
            [
                'id_ruta' => 1,
                'id_ciudad' => '730001',
                'codigo_ruta' => 1,
                'id_barrio_origen' => 3, // CENTRO
                'id_barrio_destino' => 16, // BELÉN
                'id_estado' => 1
            ],
            [
                'id_ruta' => 2,
                'id_ciudad' => '730001',
                'codigo_ruta' => 2,
                'id_barrio_origen' => 12, // 20 DE JULIO
                'id_barrio_destino' => 13, // 7 DE AGOSTO
                'id_estado' => 1
            ],
            [
                'id_ruta' => 3,
                'id_ciudad' => '730001',
                'codigo_ruta' => 4,
                'id_barrio_origen' => 5, // ESTACION
                'id_barrio_destino' => 6, // INTERLAKEN
                'id_estado' => 1
            ],
            [
                'id_ruta' => 4,
                'id_ciudad' => '730001',
                'codigo_ruta' => 6,
                'id_barrio_origen' => 6, // INTERLAKEN
                'id_barrio_destino' => 8, // LIBERTADOR
                'id_estado' => 1
            ],
            [
                'id_ruta' => 5,
                'id_ciudad' => '730001',
                'codigo_ruta' => 8,
                'id_barrio_origen' => 9, // POLA PARTE ALTA
                'id_barrio_destino' => 7, // LA POLA
                'id_estado' => 1
            ],
            [
                'id_ruta' => 6,
                'id_ciudad' => '730001',
                'codigo_ruta' => 9,
                'id_barrio_origen' => 14, // ALASKA
                'id_barrio_destino' => 15, // ANCON
                'id_estado' => 1
            ],
            [
                'id_ruta' => 7,
                'id_ciudad' => '730001',
                'codigo_ruta' => 11,
                'id_barrio_origen' => 10, // PUEBLO NUEVO
                'id_barrio_destino' => 11, // SAN PEDRO ALEJANDRINO
                'id_estado' => 1
            ],
            [
                'id_ruta' => 8,
                'id_ciudad' => '730001',
                'codigo_ruta' => 14,
                'id_barrio_origen' => 14, // ALASKA
                'id_barrio_destino' => 12, // 20 DE JULIO
                'id_estado' => 1
            ],
            [
                'id_ruta' => 9,
                'id_ciudad' => '730001',
                'codigo_ruta' => 15,
                'id_barrio_origen' => 9, // POLA PARTE ALTA
                'id_barrio_destino' => 8, // LIBERTADOR
                'id_estado' => 1
            ],
            [
                'id_ruta' => 10,
                'id_ciudad' => '730001',
                'codigo_ruta' => 17,
                'id_barrio_origen' => 19, // CENTENARIO
                'id_barrio_destino' => 8, // LIBERTADOR
                'id_estado' => 1
            ],
            [
                'id_ruta' => 11,
                'id_ciudad' => '730001',
                'codigo_ruta' => 18,
                'id_barrio_origen' => 17, // BELENCITO (usado como PICALEÑA)
                'id_barrio_destino' => 16, // BELÉN
                'id_estado' => 1
            ],
            [
                'id_ruta' => 12,
                'id_ciudad' => '730001',
                'codigo_ruta' => 19,
                'id_barrio_origen' => 6, // INTERLAKEN (usado como ARBOLEDA CAMPESTRE)
                'id_barrio_destino' => 18, // BELENCITO (usado como GALÁN)
                'id_estado' => 1
            ],
            [
                'id_ruta' => 13,
                'id_ciudad' => '730001',
                'codigo_ruta' => 20,
                'id_barrio_origen' => 3, // CENTRO (usado como DELICIAS)
                'id_barrio_destino' => 20, // CERRO PAN DE AZUCAR (usado como VENECIA)
                'id_estado' => 1
            ],
            [
                'id_ruta' => 14,
                'id_ciudad' => '730001',
                'codigo_ruta' => 21,
                'id_barrio_origen' => 10, // PUEBLO NUEVO (usado como LA CEIBITA)
                'id_barrio_destino' => 12, // 20 DE JULIO (usado como CALLE 10)
                'id_estado' => 1
            ],
            [
                'id_ruta' => 15,
                'id_ciudad' => '730001',
                'codigo_ruta' => 22,
                'id_barrio_origen' => 4, // COMBEIMA (usado como MODELIA)
                'id_barrio_destino' => 7, // LA POLA
                'id_estado' => 1
            ],
            [
                'id_ruta' => 16,
                'id_ciudad' => '730001',
                'codigo_ruta' => 23,
                'id_barrio_origen' => 9, // POLA PARTE ALTA (usado como PROTECHO)
                'id_barrio_destino' => 5, // ESTACION (usado como LA FLORIDA)
                'id_estado' => 1
            ],
            [
                'id_ruta' => 17,
                'id_ciudad' => '730001',
                'codigo_ruta' => 24,
                'id_barrio_origen' => 14, // ALASKA (usado como NUEVA CASTILLA)
                'id_barrio_destino' => 15, // ANCON (usado como CALLE 12)
                'id_estado' => 1
            ],
            [
                'id_ruta' => 18,
                'id_ciudad' => '730001',
                'codigo_ruta' => 28,
                'id_barrio_origen' => 20, // CERRO PAN DE AZUCAR (usado como PEAJE GUALANDAY)
                'id_barrio_destino' => 17, // BELENCITO (usado como YULDAIMA)
                'id_estado' => 1
            ],
            [
                'id_ruta' => 19,
                'id_ciudad' => '730001',
                'codigo_ruta' => 29,
                'id_barrio_origen' => 2, // BALTAZAR (usado como CALAMBEO)
                'id_barrio_destino' => 1, // AUGUSTO E MEDINA (usado como CARMEN DE BULIRA)
                'id_estado' => 1
            ],
            [
                'id_ruta' => 20,
                'id_ciudad' => '730001',
                'codigo_ruta' => 31,
                'id_barrio_origen' => 10, // PUEBLO NUEVO (usado como LA CEIBITA)
                'id_barrio_destino' => 12, // 20 DE JULIO (usado como CALLE 10)
                'id_estado' => 1
            ],
        ]);
    }
}
