<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GestorSetpTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 0. Asegurar que tipo_empresa 5 (SETP) exista
        $tipoExiste = DB::table('tipo_empresa')->where('id_tipo_empresa', 5)->exists();
        if (!$tipoExiste) {
            DB::table('tipo_empresa')->insert([
                'id_tipo_empresa' => 5,
                'nombre_tipo' => 'SETP',
            ]);
            $this->command->info('Tipo de Empresa SETP (5) creado en la base de datos.');
        }

        // 1. Crear Empresa SETP (id_tipo_empresa = 5)
        $nitSetp = 800999888;

        $empresaExiste = DB::table('empresa')->where('NIT', $nitSetp)->exists();
        if (!$empresaExiste) {
            DB::table('empresa')->insert([
                'NIT' => $nitSetp,
                'nombre_empresa' => 'Ente Gestor SETP Prueba',
                'telefono_empresa' => '3000000000',
                'correo_corporativo' => 'contacto@setpprueba.gov.co',
                'doc_representante' => 12345678,
                'primer_nombre_repre' => 'Admin',
                'primer_apellido_repre' => 'Setp',
                'telefono_representante' => '3000000000',
                'correo_representante' => 'admin@setpprueba.gov.co',
                'id_ciudad' => 730001,
                'id_estado' => 1,
                'id_tipo_empresa' => 5, // Tipo SETP
            ]);
            $this->command->info('Empresa SETP creada con NIT: ' . $nitSetp);
        }
        else {
            $this->command->info('La Empresa SETP ya existía (NIT: ' . $nitSetp . ').');
        }

        // 2. Crear Gestor SETP (Rol / Tipo de Usuario = 11 según el controlador real)
        $docGestor = 1040506070;

        $gestorExiste = DB::table('usuario')->where('doc_usuario', $docGestor)->exists();
        if (!$gestorExiste) {
            DB::table('usuario')->insert([
                'doc_usuario' => $docGestor,
                'NIT' => $nitSetp,
                'primer_nombre' => 'Gestor',
                'segundo_nombre' => 'Oficial',
                'primer_apellido' => 'Prueba',
                'correo' => 'gestor@setpprueba.gov.co',
                'password' => Hash::make('Gestor1234.'),
                'telefono' => '3102030405',
                'id_tipo_usuario' => 6, // rol gestor setp
                'id_ciudad' => 730001,
                'id_estado' => 1,
            ]);
            $this->command->info('Gestor SETP creado con Documento: ' . $docGestor . ' y contraseña: Gestor1234.');
        }
        else {
            $this->command->info('El Gestor SETP ya existía (Documento: ' . $docGestor . ').');
        }
    }
}
