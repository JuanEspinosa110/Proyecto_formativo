<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoEmpresaSeeder extends Seeder
{
    public function run()
    {
        // Vacía la tabla y reinicia el autoincrement
        DB::table('tipo_empresa')->truncate();

        DB::table('tipo_empresa')->insert([
            ['nombre_tipo' => 'EMPRESA DE TRANSPORTE'],
            ['nombre_tipo' => 'EMPRESA DE MANTENIMIENTO'],
            ['nombre_tipo' => 'EMPRESA DE RECARGA'],
        ]);
    }
}
