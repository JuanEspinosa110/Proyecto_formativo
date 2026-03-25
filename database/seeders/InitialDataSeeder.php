<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        // Estados completos
        // Catálogo Normalizado de Estados (Sincronizado con la lógica de negocio)
        $estados = [
            ['id_estado' => 1, 'nombre_estado' => 'ACTIVO', 'descripcion' => 'Operativo, Aprobado o Vigente'],
            ['id_estado' => 2, 'nombre_estado' => 'INACTIVO', 'descripcion' => 'No operativo o Archivado'],
            ['id_estado' => 3, 'nombre_estado' => 'SUSPENDIDO', 'descripcion' => 'Suspendido temporalmente'],
            ['id_estado' => 4, 'nombre_estado' => 'EN CURSO', 'descripcion' => 'En servicio, proceso o taller'],
            ['id_estado' => 5, 'nombre_estado' => 'PENDIENTE', 'descripcion' => 'En espera de atención o proceso'],
            ['id_estado' => 6, 'nombre_estado' => 'VENCIDO', 'descripcion' => 'Expirado, caducado o no ejecutado'],
            ['id_estado' => 7, 'nombre_estado' => 'FINALIZADO', 'descripcion' => 'Tarea, viaje o proceso completado'],
            ['id_estado' => 8, 'nombre_estado' => 'RECHAZADO', 'descripcion' => 'No aprobado por el administrador'],
            ['id_estado' => 9, 'nombre_estado' => 'BLOQUEADO', 'descripcion' => 'Restringido por seguridad o falla'],
        ];
        foreach ($estados as $estado) {
            DB::table('estado')->updateOrInsert(
                ['id_estado' => $estado['id_estado']],
                [
                    'nombre_estado' => $estado['nombre_estado'],
                    'descripcion' => $estado['descripcion'] ?? null
                ]
            );
        }

        // 2. Departamento & Ciudad
        DB::table('departamento')->insertOrIgnore(['id_departamento' => '73', 'nombre_departamento' => 'TOLIMA']);
        DB::table('ciudad')->insertOrIgnore(['id_ciudad' => '730001', 'nombre_city' => 'IBAGUE', 'id_departamento' => '73']);

        // 3. Tipos (Los IDs principales se manejan en seeders específicos)
        // Se mantienen aquí solo si son necesarios para la empresa de prueba inicial


        // 4. Empresa de prueba
        DB::table('empresa')->insertOrIgnore([
            'NIT' => 900123456,
            'nombre_empresa' => 'TRANSPORTE TEST SAS',
            'doc_representante' => 1000000001,
            'primer_nombre_repre' => 'REPRE',
            'primer_apellido_repre' => 'TEST',
            'id_tipo_empresa' => 1,
            'id_ciudad' => '730001',
            'id_estado' => 1,
        ]);

        // 5. Barrio (Ibagué)
        DB::table('barrio')->insertOrIgnore([
            ['id_barrio' => 1, 'nombre' => 'EL SALADO', 'id_ciudad' => '730001'],
            ['id_barrio' => 2, 'nombre' => 'PICALEÑA', 'id_ciudad' => '730001'],
            ['id_barrio' => 3, 'nombre' => 'CENTRO', 'id_ciudad' => '730001'],
            ['id_barrio' => 4, 'nombre' => 'JORDAN', 'id_ciudad' => '730001'],
        ]);
        // 5. Super Administrador
        DB::table('super_administrador')->insertOrIgnore([
            'doc_super_admin' => 1000000001,
            'nombre' => 'Admin Sistema',
            'correo' => 'admin@sigu.com',
            'telefono' => '3001234567',
            'password' => \Illuminate\Support\Facades\Hash::make('Admin123*'),
            'id_estado' => 1,
            'fecha_creacion' => now(),
        ]);

        // 6. Planes de Licencia
        DB::table('planes_licencia')->insertOrIgnore([
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
        // 7. Rutas (Ibagué)
        DB::table('ruta')->insertOrIgnore([
            [
                'id_ruta' => 123456,
                'codigo_ruta' => 1,
                'id_ciudad' => '730001',
                'id_barrio_origen' => 1,
                'id_barrio_destino' => 3,
                'id_estado' => 1
            ],
            [
                'id_ruta' => 654321,
                'codigo_ruta' => 2,
                'id_ciudad' => '730001',
                'id_barrio_origen' => 3,
                'id_barrio_destino' => 4,
                'id_estado' => 1
            ]
        ]);
    }
}
