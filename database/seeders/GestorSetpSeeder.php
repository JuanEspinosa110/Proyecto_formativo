<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GestorSetpSeeder extends Seeder
{
    public function run()
    {
        // Gestor SETP (ejemplo)
        DB::table('usuario')->insertOrIgnore([
            [
                'doc_usuario' => 5000000001,
                'NIT' => 800123456, // NIT diferente para SETP
                'primer_nombre' => 'Gestor',
                'segundo_nombre' => 'SETP',
                'primer_apellido' => 'Central',
                'segundo_apellido' => 'Movilidad',
                'correo' => 'gestor.setp@setp.com',
                'password' => Hash::make('GestorSetp123*'),
                'telefono' => '3200000000',
                'fecha_nacimiento' => '1985-01-01',
                'foto_usuario' => null,
                'id_tipo_usuario' => 6, // SETP
                'id_ciudad' => '730001',
                'id_estado' => 1
            ]
        ]);
    }
}
