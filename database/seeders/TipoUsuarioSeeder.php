<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoUsuarioSeeder extends Seeder
{
    public function run()
    {
        // Vacía la tabla y reinicia el autoincrement
        DB::table('tipo_usuario')->truncate();

        DB::table('tipo_usuario')->insert([
            ['nombre_tipo' => 'Admin'],
            ['nombre_tipo' => 'Pasajero'],
            ['nombre_tipo' => 'CONDUCTOR'],
            ['nombre_tipo' => 'AUXILIAR EMPRESA'],
            ['nombre_tipo' => 'PROPIETARIO'],
            ['nombre_tipo' => 'SETP'],
            ['nombre_tipo' => 'COORDINADOR BUS'],
            ['nombre_tipo' => 'GANAGANA'],
        ]);
    }
}
