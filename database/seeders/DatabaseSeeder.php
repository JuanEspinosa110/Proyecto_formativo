<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TipoEmpresaSeeder::class ,
            TipoUsuarioSeeder::class ,
            TipoDocumentoSeeder::class ,
            InitialDataSeeder::class ,
            DepartamentoSeeder::class ,
            CiudadSeeder::class ,
            BarrioSeeder::class ,

            EmpresaSeeder::class ,
            UsuarioEmpresaSeeder::class ,
            LicenciaEmpresaSeeder::class ,
            BusSeeder::class ,
            SuperAdministradorSeeder::class ,
            GestorSetpSeeder::class ,
            RutaSeeder::class ,
        ]);

    // User::create([
    //     'doc_usuario' => 1000000000,
    //     'primer_nombre' => 'Test',
    //     'primer_apellido' => 'User',
    //     'correo' => 'test@example.com',
    //     'password' => \Illuminate\Support\Facades\Hash::make('password'),
    //     'id_tipo_usuario' => 1,
    //     'id_estado' => 1
    // ]);
    }
}
