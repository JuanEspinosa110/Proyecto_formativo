<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConductorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $conductores = [
            [
                'doc_usuario' => 1073456789,
                'primer_nombre' => 'Carlos',
                'primer_apellido' => 'Rodríguez',
                'correo' => 'carlos.conductor@sigu.com',
                'telefono' => '3109876543',
            ],
            [
                'doc_usuario' => 1085123456,
                'primer_nombre' => 'María',
                'primer_apellido' => 'López',
                'correo' => 'maria.conductora@sigu.com',
                'telefono' => '3214567890',
            ],
            [
                'doc_usuario' => 1099888777,
                'primer_nombre' => 'Jorge',
                'primer_apellido' => 'García',
                'correo' => 'jorge.conductor@sigu.com',
                'telefono' => '3001112233',
            ],
        ];

        foreach ($conductores as $data) {
            \App\Models\Usuario::updateOrCreate(
            ['doc_usuario' => $data['doc_usuario']],
            [
                'NIT' => '9004567890',
                'primer_nombre' => $data['primer_nombre'],
                'primer_apellido' => $data['primer_apellido'],
                'correo' => $data['correo'],
                'password' => \Illuminate\Support\Facades\Hash::make('conductor123'),
                'telefono' => $data['telefono'],
                'id_tipo_usuario' => 3, // Conductor
                'id_ciudad' => '730001', // ibague
                'id_estado' => 1, // Activo
            ]
            );
        }
    }
}
