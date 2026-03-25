<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmpresaSeeder extends Seeder
{
    public function run()
    {
        // EMPRESA DE TRANSPORTE 1 (EXISTENTE)
        DB::table('empresa')->updateOrInsert(
        ['NIT' => 900123456],
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
            'id_tipo_empresa' => 1,
            'id_ciudad' => '730001',
            'id_estado' => 1,
            'fecha_creacion' => now(),
        ]
        );

        // --- OTRAS 4 EMPRESAS DE TRANSPORTE ---

        DB::table('empresa')->updateOrInsert(
        ['NIT' => 900111222],
        [
            'nombre_empresa' => 'EXPRESO IBAGUÉ S.A.',
            'doc_representante' => 1100111222,
            'primer_nombre_repre' => 'JUAN',
            'segundo_nombre_repre' => 'CARLOS',
            'primer_apellido_repre' => 'PÉREZ',
            'segundo_apellido_repre' => 'GÓMEZ',
            'telefono_representante' => '3201112233',
            'correo_representante' => 'jcperez@expresoibague.com',
            'telefono_empresa' => '3102223344',
            'correo_corporativo' => 'contacto@expresoibague.com',
            'id_tipo_empresa' => 1,
            'id_ciudad' => '730001',
            'id_estado' => 1,
            'fecha_creacion' => now(),
        ]
        );

        DB::table('empresa')->updateOrInsert(
        ['NIT' => 900333444],
        [
            'nombre_empresa' => 'COTRAUTOL S.A.S.',
            'doc_representante' => 1100333444,
            'primer_nombre_repre' => 'LUZ',
            'segundo_nombre_repre' => 'MARINA',
            'primer_apellido_repre' => 'RODRÍGUEZ',
            'segundo_apellido_repre' => 'ARIAS',
            'telefono_representante' => '3213334455',
            'correo_representante' => 'lmrodriguez@cotrautol.com',
            'telefono_empresa' => '3114445566',
            'correo_corporativo' => 'atencion@cotrautol.com',
            'id_tipo_empresa' => 1,
            'id_ciudad' => '730001',
            'id_estado' => 1,
            'fecha_creacion' => now(),
        ]
        );

        DB::table('empresa')->updateOrInsert(
        ['NIT' => 900555666],
        [
            'nombre_empresa' => 'TRANSPORTES LA IBAGUEREÑA S.A.S.',
            'doc_representante' => 1100555666,
            'primer_nombre_repre' => 'RICARDO',
            'segundo_nombre_repre' => 'ALFONSO',
            'primer_apellido_repre' => 'LÓPEZ',
            'segundo_apellido_repre' => 'DÍAZ',
            'telefono_representante' => '3125556677',
            'correo_representante' => 'ralopez@laibaguereña.com',
            'telefono_empresa' => '3006667788',
            'correo_corporativo' => 'gerencia@laibaguereña.com',
            'id_tipo_empresa' => 1,
            'id_ciudad' => '730001',
            'id_estado' => 1,
            'fecha_creacion' => now(),
        ]
        );

        DB::table('empresa')->updateOrInsert(
        ['NIT' => 900777888],
        [
            'nombre_empresa' => 'EXPRESO PURIFICACIÓN S.A.',
            'doc_representante' => 1100777888,
            'primer_nombre_repre' => 'ANA',
            'segundo_nombre_repre' => 'MILENA',
            'primer_apellido_repre' => 'MARTÍNEZ',
            'segundo_apellido_repre' => 'ROJAS',
            'telefono_representante' => '3137778899',
            'correo_representante' => 'amartinez@expresopurificacion.com',
            'telefono_empresa' => '3148889900',
            'correo_corporativo' => 'contacto@expresopurificacion.com',
            'id_tipo_empresa' => 1,
            'id_ciudad' => '730001',
            'id_estado' => 1,
            'fecha_creacion' => now(),
        ]
        );

        // --- 1 EMPRESA DE RECARGA ---

        DB::table('empresa')->updateOrInsert(
        ['NIT' => 800222333],
        [
            'nombre_empresa' => 'SUPERGIROS GANA GANA',
            'doc_representante' => 1200222333,
            'primer_nombre_repre' => 'LUIS',
            'segundo_nombre_repre' => 'FERNANDO',
            'primer_apellido_repre' => 'CASTRO',
            'segundo_apellido_repre' => 'RUIZ',
            'telefono_representante' => '3002223344',
            'correo_representante' => 'lfcastro@pagatodo.com',
            'telefono_empresa' => '3005556677',
            'correo_corporativo' => 'pagatodo.recaudo@pagatodo.com',
            'id_tipo_empresa' => 3,
            'id_ciudad' => '730001',
            'id_estado' => 1,
            'fecha_creacion' => now(),
        ]
        );

        // --- 6 EMPRESAS DE MANTENIMIENTO ---

        DB::table('empresa')->updateOrInsert(
        ['NIT' => 700444555],
        [
            'nombre_empresa' => 'TALLER EL MOTORISTA TOLIMENSE',
            'doc_representante' => 1300444555,
            'primer_nombre_repre' => 'PEDRO',
            'segundo_nombre_repre' => 'JOSÉ',
            'primer_apellido_repre' => 'SÁNCHEZ',
            'segundo_apellido_repre' => 'ORTIZ',
            'telefono_representante' => '3154445566',
            'correo_representante' => 'psanchez@tallerelmotorista.com',
            'telefono_empresa' => '3165556677',
            'correo_corporativo' => 'servicios@tallerelmotorista.com',
            'id_tipo_empresa' => 2,
            'id_ciudad' => '730001',
            'id_estado' => 1,
            'fecha_creacion' => now(),
        ]
        );

        DB::table('empresa')->updateOrInsert(
        ['NIT' => 700666777],
        [
            'nombre_empresa' => 'CENTRO DE SERVICIO TÉCNICO DIESEL',
            'doc_representante' => 1300666777,
            'primer_nombre_repre' => 'SOFÍA',
            'segundo_nombre_repre' => 'ISABEL',
            'primer_apellido_repre' => 'CASTRO',
            'segundo_apellido_repre' => 'MÉNDEZ',
            'telefono_representante' => '3176667788',
            'correo_representante' => 'scastro@dieseltolima.com',
            'telefono_empresa' => '3187778899',
            'correo_corporativo' => 'soporte@dieseltolima.com',
            'id_tipo_empresa' => 2,
            'id_ciudad' => '730001',
            'id_estado' => 1,
            'fecha_creacion' => now(),
        ]
        );

        DB::table('empresa')->updateOrInsert(
        ['NIT' => 700888999],
        [
            'nombre_empresa' => 'FRENOS Y SUSPENSIONES DE LA 60',
            'doc_representante' => 1300888999,
            'primer_nombre_repre' => 'JAVIER',
            'segundo_nombre_repre' => 'EDUARDO',
            'primer_apellido_repre' => 'RUIZ',
            'segundo_apellido_repre' => 'BLANCO',
            'telefono_representante' => '3198889900',
            'correo_representante' => 'jruiz@frenosla60.com',
            'telefono_empresa' => '3009990011',
            'correo_corporativo' => 'ventas@frenosla60.com',
            'id_tipo_empresa' => 2,
            'id_ciudad' => '730001',
            'id_estado' => 1,
            'fecha_creacion' => now(),
        ]
        );

        DB::table('empresa')->updateOrInsert(
        ['NIT' => 701000111],
        [
            'nombre_empresa' => 'ELECTROMECÁNICA Y BOBINADOS',
            'doc_representante' => 1301000111,
            'primer_nombre_repre' => 'ELENA',
            'segundo_nombre_repre' => 'PATRICIA',
            'primer_apellido_repre' => 'GÓMEZ',
            'segundo_apellido_repre' => 'VARGAS',
            'telefono_representante' => '3210001122',
            'correo_representante' => 'egomez@electromecanica.com',
            'telefono_empresa' => '3212223344',
            'correo_corporativo' => 'contacto@electromecanica.com',
            'id_tipo_empresa' => 2,
            'id_ciudad' => '730001',
            'id_estado' => 1,
            'fecha_creacion' => now(),
        ]
        );

        DB::table('empresa')->updateOrInsert(
        ['NIT' => 701222333],
        [
            'nombre_empresa' => 'MULTISERVICIOS AUTOMOTRICES',
            'doc_representante' => 1301222333,
            'primer_nombre_repre' => 'JORGE',
            'segundo_nombre_repre' => 'ELIÉCER',
            'primer_apellido_repre' => 'HERRERA',
            'segundo_apellido_repre' => 'ROJAS',
            'telefono_representante' => '3222223344',
            'correo_representante' => 'jherrera@multiservicios.com',
            'telefono_empresa' => '3224445566',
            'correo_corporativo' => 'administracion@multiservicios.com',
            'id_tipo_empresa' => 2,
            'id_ciudad' => '730001',
            'id_estado' => 1,
            'fecha_creacion' => now(),
        ]
        );

        DB::table('empresa')->updateOrInsert(
        ['NIT' => 701444555],
        [
            'nombre_empresa' => 'TECNIAUTOS Y PINTURAS DEL TOLIMA',
            'doc_representante' => 1301444555,
            'primer_nombre_repre' => 'MARTA',
            'segundo_nombre_repre' => 'LUCÍA',
            'primer_apellido_repre' => 'ORTIZ',
            'segundo_apellido_repre' => 'PEÑA',
            'telefono_representante' => '3234445566',
            'correo_representante' => 'mortiz@tecniautos.com',
            'telefono_empresa' => '3236667788',
            'correo_corporativo' => 'pintura@tecniautos.com',
            'id_tipo_empresa' => 2,
            'id_ciudad' => '730001',
            'id_estado' => 1,
            'fecha_creacion' => now(),
        ]
        );

        // SETP IBAGUÉ (EXISTENTE)
        DB::table('empresa')->updateOrInsert(
        ['NIT' => 600123456],
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
            'id_tipo_empresa' => 4,
            'id_ciudad' => '730001',
            'id_estado' => 1,
            'fecha_creacion' => now(),
        ]
        );


    }
}
