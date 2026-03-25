<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoEmpresaSeeder extends Seeder
{
    public function run()
    {
        DB::table('tipo_empresa')->insertOrIgnore([
            ['id_tipo_empresa' => 1, 'nombre_tipo' => 'EMPRESA DE TRANSPORTE'],
            ['id_tipo_empresa' => 2, 'nombre_tipo' => 'EMPRESA DE MANTENIMIENTO'],
            ['id_tipo_empresa' => 3, 'nombre_tipo' => 'EMPRESA DE RECARGA'],
            ['id_tipo_empresa' => 4, 'nombre_tipo' => 'SETP - SISTEMA ESTRATEGICO DE TRANSPORTE PUBLICO'],
        ]);
    }
}
