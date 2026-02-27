<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Estados
        DB::table('estado')->insert([
            ['id_estado' => 1, 'nombre_estado' => 'ACTIVO', 'descripcion' => 'Operativo'],
            ['id_estado' => 2, 'nombre_estado' => 'INACTIVO', 'descripcion' => 'No operativo'],
        ]);

        // 2. Departamento & Ciudad
        DB::table('departamento')->insert(['id_departamento' => '15', 'nombre_departamento' => 'BOYACA']);
        DB::table('ciudad')->insert(['id_ciudad' => '15001', 'nombre_city' => 'TUNJA', 'id_departamento' => '15']);

        // 3. Barrio
        DB::table('barrio')->insert([
            ['id_barrio' => 1, 'nombre' => 'CENTRO', 'id_ciudad' => '15001'],
            ['id_barrio' => 2, 'nombre' => 'NORTE', 'id_ciudad' => '15001'],
        ]);

        // 4. Tipos
        DB::table('tipo_empresa')->insert(['id_tipo_empresa' => 1, 'nombre_tipo' => 'COOPERATIVA']);
        DB::table('tipo_usuario')->insert(['id_tipo_usuario' => 1, 'nombre_tipo' => 'ADMINISTRADOR']);
        // 5. Super Administrador
        DB::table('super_administrador')->insert([
            'doc_super_admin' => 1000000001,
            'nombre' => 'Admin Sistema',
            'correo' => 'admin@sigu.com',
            'telefono' => '3001234567',
            'password' => \Illuminate\Support\Facades\Hash::make('Admin123*'),
            'id_estado' => 1,
            'fecha_creacion' => now(),
        ]);

        // 6. Planes de Licencia
        DB::table('planes_licencia')->insert([
            [
                'id_plan' => 1, 
                'nombre_plan' => 'Básico', 
                'duracion_meses' => 1, 
                'precio' => 50000, 
                'descripcion' => 'Ideal para pequeñas empresas de transporte.',
                'id_estado' => 1
            ],
            [
                'id_plan' => 2, 
                'nombre_plan' => 'Profesional', 
                'duracion_meses' => 6, 
                'precio' => 250000, 
                'descripcion' => 'Gestión completa para flotas medianas.',
                'id_estado' => 1
            ],
            [
                'id_plan' => 3, 
                'nombre_plan' => 'Premium', 
                'duracion_meses' => 12, 
                'precio' => 450000, 
                'descripcion' => 'Control total y reportes avanzados.',
                'id_estado' => 1
            ],
        ]);
    }
}
