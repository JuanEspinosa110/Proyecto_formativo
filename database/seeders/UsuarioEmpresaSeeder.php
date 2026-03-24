<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioEmpresaSeeder extends Seeder
{
    public function run()
    {
        // Empresa 1: TRANSPORTE TEST SAS
        // ADMINISTRADOR (doc_usuario inicia en 1)
        DB::table('usuario')->insertOrIgnore([
            [
                'doc_usuario' => 1000000001,
                'NIT' => 900123456,
                'primer_nombre' => 'REPRE',
                'segundo_nombre' => 'PRUEBA',
                'primer_apellido' => 'TEST',
                'segundo_apellido' => 'EMPRESA',
                'correo' => 'admin@transportetest.com',
                'password' => Hash::make('Admin123*'),
                'telefono' => '3001234567',
                'fecha_nacimiento' => '1980-01-01',
                'foto_usuario' => null,
                'id_tipo_usuario' => 1, // ADMINISTRADOR
                'id_ciudad' => '730001',
                'id_estado' => 1
            ],
            // PROPIETARIOS (doc_usuario inicia en 2)
            [
                'doc_usuario' => 2000000002,
                'NIT' => 900123456,
                'primer_nombre' => 'Pedro',
                'segundo_nombre' => 'Luis',
                'primer_apellido' => 'Gomez',
                'segundo_apellido' => 'Propietario',
                'correo' => 'propietario@empresa.com',
                'password' => Hash::make('Propietario123*'),
                'telefono' => '3001112222',
                'fecha_nacimiento' => '1975-05-10',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5, // PROPIETARIO
                'id_ciudad' => '730001',
                'id_estado' => 1
            ],
            [
                'doc_usuario' => 2000000003,
                'NIT' => 900123456,
                'primer_nombre' => 'Laura',
                'segundo_nombre' => 'Sofia',
                'primer_apellido' => 'Martinez',
                'segundo_apellido' => 'Propietaria',
                'correo' => 'propietaria2@empresa.com',
                'password' => Hash::make('Propietaria123*'),
                'telefono' => '3002223333',
                'fecha_nacimiento' => '1985-03-22',
                'foto_usuario' => null,
                'id_tipo_usuario' => 5, // PROPIETARIO
                'id_ciudad' => '730001',
                'id_estado' => 1
            ],
            // CONDUCTORES (doc_usuario inicia en 3)
            [
                'doc_usuario' => 3000000003,
                'NIT' => 900123456,
                'primer_nombre' => 'Carlos',
                'segundo_nombre' => 'Eduardo',
                'primer_apellido' => 'Lopez',
                'segundo_apellido' => 'Conductor',
                'correo' => 'conductor@empresa.com',
                'password' => Hash::make('Conductor123*'),
                'telefono' => '3003334444',
                'fecha_nacimiento' => '1982-08-15',
                'foto_usuario' => null,
                'id_tipo_usuario' => 3, // CONDUCTOR
                'id_ciudad' => '730001',
                'id_estado' => 1
            ],
            [
                'doc_usuario' => 3000000004,
                'NIT' => 900123456,
                'primer_nombre' => 'Miguel',
                'segundo_nombre' => 'Angel',
                'primer_apellido' => 'Ramirez',
                'segundo_apellido' => 'Conductor',
                'correo' => 'conductor2@empresa.com',
                'password' => Hash::make('Conductor456*'),
                'telefono' => '3004445555',
                'fecha_nacimiento' => '1987-11-30',
                'foto_usuario' => null,
                'id_tipo_usuario' => 3, // CONDUCTOR
                'id_ciudad' => '730001',
                'id_estado' => 1
            ],
            // AUXILIAR EMPRESA (doc_usuario inicia en 4)
            [
                'doc_usuario' => 4000000004,
                'NIT' => 900123456,
                'primer_nombre' => 'Ana',
                'segundo_nombre' => 'Maria',
                'primer_apellido' => 'Ruiz',
                'segundo_apellido' => 'Auxiliar',
                'correo' => 'auxiliar@empresa.com',
                'password' => Hash::make('Auxiliar123*'),
                'telefono' => '3005556666',
                'fecha_nacimiento' => '1990-12-20',
                'foto_usuario' => null,
                'id_tipo_usuario' => 4, // AUXILIAR EMPRESA
                'id_ciudad' => '730001',
                'id_estado' => 1
            ]
        ]);
    }
}
