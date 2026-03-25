<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoUsuarioSeeder extends Seeder
{
    public function run()
    {
        DB::table('tipo_usuario')->insertOrIgnore([
            ['id_tipo_usuario' => 1, 'nombre_tipo' => 'ADMINISTRADOR'],
            ['id_tipo_usuario' => 2, 'nombre_tipo' => 'Pasajero'],
            ['id_tipo_usuario' => 3, 'nombre_tipo' => 'CONDUCTOR'],
            ['id_tipo_usuario' => 4, 'nombre_tipo' => 'AUXILIAR EMPRESA'],
            ['id_tipo_usuario' => 5, 'nombre_tipo' => 'PROPIETARIO'],
            ['id_tipo_usuario' => 6, 'nombre_tipo' => 'SETP'],
            ['id_tipo_usuario' => 7, 'nombre_tipo' => 'COORDINADOR BUS'],
<<<<<<< HEAD
            ['id_tipo_usuario' => 8, 'nombre_tipo' => 'GESTOR DE RECARGAS'],
=======
            ['id_tipo_usuario' => 8, 'nombre_tipo' => 'GANAGANA'],
>>>>>>> develop
            ['id_tipo_usuario' => 9, 'nombre_tipo' => 'JEFE DE MANTENIMIENTO'],

        ]);
    }
}
