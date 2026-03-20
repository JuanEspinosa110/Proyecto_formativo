<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoEmpresaSeeder extends Seeder
{
    public function run()
    {
        DB::table('tipo_empresa')->insertOrIgnore([
            ['nombre_tipo' => 'EMPRESA DE TRANSPORTE'],
            ['nombre_tipo' => 'EMPRESA DE MANTENIMIENTO'],
            ['nombre_tipo' => 'EMPRESA DE RECARGA'],
        ]);
    }
}
