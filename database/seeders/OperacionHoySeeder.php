<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class OperacionHoySeeder extends Seeder
{
    public function run(): void
    {
        $nit = 900123456; // Como entero
        $id_ruta = 1;

        echo "1. Empresa...\n";
        DB::table('empresa')->updateOrInsert(['NIT' => $nit], [
            'nombre_empresa' => 'TRANSPORTE TEST SAS',
            'id_tipo_empresa' => 1,
            'id_estado' => 1,
            'id_ciudad' => 730001
        ]);

        $docPropietario = 1007899000;
        echo "2. Propietario...\n";
        DB::table('usuario')->updateOrInsert(['doc_usuario' => $docPropietario], [
            'primer_nombre' => 'RICARDO',
            'primer_apellido' => 'GOMEZ',
            'correo' => 'propietario.ricardo@gmail.com',
            'id_tipo_usuario' => 5,
            'id_estado' => 1,
            'password' => Hash::make('secret123'),
            'NIT' => $nit,
            'fecha_nacimiento' => '1980-01-01'
        ]);

        echo "3. Conductores...\n";
        $docs = [1007899111, 1007899222];
        foreach ($docs as $d) {
            DB::table('usuario')->updateOrInsert(['doc_usuario' => $d], [
                'primer_nombre' => 'CONDUCTOR',
                'primer_apellido' => 'TEST',
                'correo' => "conductor.{$d}@gmail.com",
                'id_tipo_usuario' => 3,
                'id_estado' => 1,
                'password' => Hash::make('secret123'),
                'NIT' => $nit,
                'fecha_nacimiento' => '1990-01-01'
            ]);
        }

        echo "4. Buses...\n";
        $placas = ['NUEVO-01', 'NUEVO-02'];
        foreach ($placas as $idx => $placa) {
            echo "   Bus $placa...\n";
            DB::table('bus')->updateOrInsert(['placa' => $placa], [
                'NIT' => $nit,
                'modelo' => 'Toyota 2024',
                'capacidad_pasajeros' => 20,
                'kilometraje' => 0,
                'id_estado' => 1,
                'linc_transito' => (int)(800000 + $idx),
                'numero_chasis' => 'CH' . $idx . $nit,
                'numero_motor' => 'MT' . $idx . $nit,
                'doc_propietario' => $docPropietario,
                'nombre_propietario' => 'RICARDO GOMEZ',
                'telefono' => '3120001122',
                'correo' => 'bus@test.com'
            ]);

            echo "   Docs $placa...\n";
            DB::table('documentos')->updateOrInsert(['placa' => $placa, 'id_tipo_documento' => 1], [
                'nombre' => 'SOAT',
                'archivo' => 'seeds/soat.pdf',
                'fecha_expedicion' => '2024-01-01',
                'fecha_vencimiento' => '2025-01-01',
                'NIT' => $nit,
                'id_estado' => 1
            ]);
        }

        echo "5. Viajes...\n";
        $maxId = DB::table('viaje')->max('id_viaje') ?? 0;
        foreach ($placas as $idx => $placa) {
            $fechaViaje = date('Y-m-d 21:00:00');
            DB::table('viaje')->updateOrInsert(
                ['placa' => $placa, 'fecha' => $fechaViaje],
                [
                    'doc_us' => $docs[$idx],
                    'id_ruta' => $id_ruta,
                    'fecha_asignacion' => date('Y-m-d H:i:s'),
                    'id_estado' => 1
                ]
            );
        }

        echo "Seeder Finalizado OK.\n";
    }
}
