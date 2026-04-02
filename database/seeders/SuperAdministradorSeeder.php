<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\SuperAdministrador;

class SuperAdministradorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SuperAdministrador::firstOrCreate(
            [
                'doc_super_admin' => 1105463369
            ],
            [
                'nombre' => 'Juanes',
                'correo' => 'juanitosexy09@gmail.com',
                'telefono' => '3228881996',
                'foto_perfil' => null,
                'password' => Hash::make('Juanes110*'),
                'id_estado' => 1,
                'id_ciudad' => '730001',
            ]
        );
        // Ejemplo de otro superadministrador
        SuperAdministrador::firstOrCreate(
            [
                'doc_super_admin' => 7521526
            ],
            [
                'nombre' => 'Luis Fernando',
                'correo' => 'luisfvl2503colombia@gmail.com',
                'telefono' => '3009700956',
                'foto_perfil' => null,
                'password' => Hash::make('Luis2801.'),
                'id_estado' => 1,
                'id_ciudad' => '730001',
            ]
        );

        SuperAdministrador::firstOrCreate(
            [
                'doc_super_admin' => 1083884051
            ],
            [
                'nombre' => 'Derly Medina',
                'correo' => 'derlymedina2807@gmail.com',
                'telefono' => '3001684566',
                'foto_perfil' => null,
                'password' => Hash::make('Derly3107.'),
                'id_estado' => 1,
                'id_ciudad' => '730001',
            ]
        );

        // Nuevo Super Administrador solicitado
        SuperAdministrador::firstOrCreate(
            [
                'doc_super_admin' => 78092026
            ],
            [
                'nombre' => 'Cesar Esquivel',
                'correo' => 'esquivel7809@gmail.com',
                'telefono' => '3100000000',
                'foto_perfil' => null,
                'password' => Hash::make('Qwerty2026-$'),
                'id_estado' => 1,
                'id_ciudad' => '730001',
            ]
        );
    }
}
