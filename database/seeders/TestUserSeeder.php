<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $docUsuario = '123456789';
        $idTarjeta = 'TARJ-TEST-001';

        // 1. Crear el Usuario Pasajero (id_tipo_usuario = 3)
        DB::table('usuario')->upsert([
            [
                'doc_usuario' => $docUsuario,
                'NIT' => null, // O el NIT de una empresa si es estricto, pero para pasajero puede ser null
                'primer_nombre' => 'Pasajero',
                'segundo_nombre' => 'De',
                'primer_apellido' => 'Prueba',
                'segundo_apellido' => 'Mapa',
                'correo' => 'pasajero@prueba.com',
                'password' => Hash::make('password123.'),
                'telefono' => '3001234567',
                'foto_usuario' => null,
                'id_tipo_usuario' => 2, // 2 corresponde a Usuario/Pasajero
                'id_ciudad' => '730001', // Ibagué
                'id_estado' => 1, // 1 Activo
            ]
        ], ['doc_usuario'], [
            'primer_nombre', 'primer_apellido', 'correo', 'password', 'id_tipo_usuario', 'id_estado', 'id_ciudad'
        ]);

        // 2. Crear la Tarjeta asociada al usuario
        DB::table('tarjeta')->upsert([
            [
                'id_tarjeta' => $idTarjeta,
                'codigo_tarjeta' => '1234-5678-9012-3456',
                'saldo' => 50000,
                'id_estado' => 1, // 1 Activa
                'doc_usuario' => $docUsuario,
            ]
        ], ['id_tarjeta'], ['codigo_tarjeta', 'saldo', 'id_estado', 'doc_usuario']);

        // 3. Crear la Titularidad de la Tarjeta
        // Como la tabla tiene un ID autoincremental, es mejor verificar si ya existe una titularidad activa
        $titularidadExists = DB::table('titularidad_tarjeta')
            ->where('id_tarjeta', $idTarjeta)
            ->where('doc_usuario', $docUsuario)
            ->where('id_estado', 1)
            ->exists();

        if (!$titularidadExists) {
            DB::table('titularidad_tarjeta')->insert([
                'id_tarjeta' => $idTarjeta,
                'doc_usuario' => $docUsuario,
                'fecha_inicio' => Carbon::now(),
                'fecha_fin' => null,
                'id_estado' => 1, // 1 Activa
                'motivo_cambio' => null,
            ]);
        }
    }
}
