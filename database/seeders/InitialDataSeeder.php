<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Estados
        DB::table('estado')->updateOrInsert(
            ['id_estado' => 1],
            ['nombre_estado' => 'ACTIVO', 'descripcion' => 'Operativo']
        );
        DB::table('estado')->updateOrInsert(
            ['id_estado' => 2],
            ['nombre_estado' => 'INACTIVO', 'descripcion' => 'No operativo']
        );

        // 2. Departamento & Ciudad
        DB::table('departamento')->updateOrInsert(
            ['id_departamento' => '73'],
            ['nombre_departamento' => 'TOLIMA']
        );
        DB::table('ciudad')->updateOrInsert(
            ['id_ciudad' => '730001'],
            ['nombre_city' => 'IBAGUE', 'id_departamento' => '73']
        );

        // 3. Tipos
        DB::table('tipo_empresa')->updateOrInsert(
            ['id_tipo_empresa' => 1],
            ['nombre_tipo' => 'COOPERATIVA']
        );
        DB::table('tipo_usuario')->updateOrInsert(
            ['id_tipo_usuario' => 1],
            ['nombre_tipo' => 'ADMINISTRADOR']
        );

        // 4. Empresa de prueba
        DB::table('empresa')->updateOrInsert(
            ['NIT' => 900123456],
            [
                'nombre_empresa' => 'TRANSPORTE TEST SAS',
                'doc_representante' => 1000000001,
                'primer_nombre_repre' => 'REPRE',
                'primer_apellido_repre' => 'TEST',
                'id_tipo_empresa' => 1,
                'id_ciudad' => '730001',
                'id_estado' => 1,
            ]
        );

        // 5. Barrio (Ibagué)
        DB::table('barrio')->updateOrInsert(
            ['id_barrio' => 1], ['nombre' => 'EL SALADO', 'id_ciudad' => '730001']
        );
        DB::table('barrio')->updateOrInsert(
            ['id_barrio' => 2], ['nombre' => 'PICALEÑA', 'id_ciudad' => '730001']
        );
        DB::table('barrio')->updateOrInsert(
            ['id_barrio' => 3], ['nombre' => 'CENTRO', 'id_ciudad' => '730001']
        );
        DB::table('barrio')->updateOrInsert(
            ['id_barrio' => 4], ['nombre' => 'JORDAN', 'id_ciudad' => '730001']
        );

        // 5. Super Administrador
        DB::table('super_administrador')->updateOrInsert(
            ['doc_super_admin' => 1000000001],
            [
                'nombre' => 'Admin Sistema',
                'correo' => 'admin@sigu.com',
                'telefono' => '3001234567',
                'password' => \Illuminate\Support\Facades\Hash::make('Admin123*'),
                'id_estado' => 1,
                'fecha_creacion' => now(),
            ]
        );

        // 6. Planes de Licencia
        DB::table('planes_licencia')->updateOrInsert(
            ['id_plan' => 1],
            [
                'nombre_plan' => 'Básico',
                'duracion_meses' => 1,
                'precio' => 50000,
                'descripcion' => 'Ideal para pequeñas empresas de transporte.',
                'id_estado' => 1
            ]
        );
        DB::table('planes_licencia')->updateOrInsert(
            ['id_plan' => 2],
            [
                'nombre_plan' => 'Profesional',
                'duracion_meses' => 6,
                'precio' => 250000,
                'descripcion' => 'Gestión completa para flotas medianas.',
                'id_estado' => 1
            ]
        );
        DB::table('planes_licencia')->updateOrInsert(
            ['id_plan' => 3],
            [
                'nombre_plan' => 'Premium',
                'duracion_meses' => 12,
                'precio' => 450000,
                'descripcion' => 'Control total y reportes avanzados.',
                'id_estado' => 1
            ]
        );

        // 7. Rutas (Ibagué)
        DB::table('ruta')->updateOrInsert(
            ['id_ruta' => 123456],
            [
                'id_ciudad' => '730001',
                'id_barrio_origen' => 1,
                'id_barrio_destino' => 3,
                'id_estado' => 1,
                'codigo_ruta' => 'RUTA-001'
            ]
        );
        DB::table('ruta')->updateOrInsert(
            ['id_ruta' => 654321],
            [
                'id_ciudad' => '730001',
                'id_barrio_origen' => 3,
                'id_barrio_destino' => 4,
                'id_estado' => 1,
                'codigo_ruta' => 'RUTA-002'
            ]
        );
    }
}
