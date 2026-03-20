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
            InitialDataSeeder::class,
            SuperAdministradorSeeder::class,
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
