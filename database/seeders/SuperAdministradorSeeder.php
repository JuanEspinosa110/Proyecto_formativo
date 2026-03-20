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
            ]
        );
    }
}
