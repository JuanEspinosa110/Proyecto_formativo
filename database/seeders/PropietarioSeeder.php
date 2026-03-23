<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

class PropietarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $docUsuario = 2222222222; // Documento de prueba para el propietario

        $usuarioExiste = Usuario::where('doc_usuario', $docUsuario)->exists();

        if (!$usuarioExiste) {
            Usuario::create([
                'doc_usuario' => $docUsuario,
                'id_tipo_usuario' => 5, // 5 = PROPIETARIO
                'primer_nombre' => 'CARLOS',
                'primer_apellido' => 'SANTANA',
                'correo' => 'propietario@correo.com',
                'password' => Hash::make('password123'),
                'id_ciudad' => '730001', // Asumiendo Ibagué
                'id_estado' => 1,
                'telefono' => '3209876543',
            ]);
        }
    }
}
