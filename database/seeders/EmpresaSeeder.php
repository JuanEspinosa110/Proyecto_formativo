<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmpresaSeeder extends Seeder
{
    public function run()
    {
        // EMPRESA DE TRANSPORTE
        DB::table('empresa')->updateOrInsert(
            [
                'NIT' => 900123456
            ],
            [
                'nombre_empresa' => 'TRANSPORTE TEST SAS',
                'doc_representante' => 1000000001,
                'primer_nombre_repre' => 'REPRE',
                'segundo_nombre_repre' => 'PRUEBA',
                'primer_apellido_repre' => 'TEST',
                'segundo_apellido_repre' => 'EMPRESA',
                'telefono_representante' => '3001234567',
                'correo_representante' => 'representante@test.com',
                'telefono_empresa' => '3101234567',
                'correo_corporativo' => 'contacto@transportetest.com',
                'id_tipo_empresa' => 1, // EMPRESA DE TRANSPORTE
                'id_ciudad' => '730001',
                'id_estado' => 1,
                'fecha_creacion' => now(),
            ]
        );

        // SETP - SISTEMA ESTRATEGICO DE TRANSPORTE PUBLICO
        DB::table('empresa')->updateOrInsert(
            [
                'NIT' => 800123456
            ],
            [
                'nombre_empresa' => 'SETP IBAGUE S.A.S',
                'doc_representante' => 5000000001,
                'primer_nombre_repre' => 'Gestor',
                'segundo_nombre_repre' => 'SETP',
                'primer_apellido_repre' => 'Central',
                'segundo_apellido_repre' => 'Movilidad',
                'telefono_representante' => '3200000000',
                'correo_representante' => 'gestor.setp@setp.com',
                'telefono_empresa' => '3200000000',
                'correo_corporativo' => 'contacto@setp.com',
                'id_tipo_empresa' => 4, // SETP
                'id_ciudad' => '730001',
                'id_estado' => 1,
                'fecha_creacion' => now(),
            ]
        );
    }
}
