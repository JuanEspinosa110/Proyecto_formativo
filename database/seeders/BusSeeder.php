<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusSeeder extends Seeder
{
    public function run()
    {
        // --- GENERACIÓN ORGÁNICA DE BUSES (8 por empresa de transporte) ---
        $empresasTransporte = [
            ['NIT' => 900123456, 'prefix' => '000'],
            ['NIT' => 900111222, 'prefix' => '111'],
            ['NIT' => 900333444, 'prefix' => '333'],
            ['NIT' => 900555666, 'prefix' => '555'],
            ['NIT' => 900777888, 'prefix' => '777'],
        ];

        foreach ($empresasTransporte as $empresa) {
            // Obtener propietarios y conductores de la empresa para asignar como dueños
            $owners = DB::table('usuario')
                ->where('NIT', $empresa['NIT'])
                ->whereIn('id_tipo_usuario', [3, 5]) // CONDUCTOR o PROPIETARIO
                ->get();

            if ($owners->isEmpty()) continue;

            $buses = [];
            $modelos = ['Mercedes-Benz', 'Volvo', 'Scania', 'Chevrolet', 'Volkswagen', 'Renault', 'Toyota', 'Hyundai'];
            
            for ($i = 0; $i < 8; $i++) {
                $owner = $owners->random();
                
                // Generar placa (3 letras + 3 números)
                $letras = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                $placa = substr($letras, rand(0, 25), 1) . substr($letras, rand(0, 25), 1) . substr($letras, rand(0, 25), 1) . rand(100, 999);
                
                $buses[] = [
                    'placa' => $placa,
                    'NIT' => $empresa['NIT'],
                    'modelo' => $modelos[$i] . ' ' . (2018 + rand(0, 6)),
                    'capacidad_pasajeros' => rand(32, 42),
                    'kilometraje' => rand(10000, 80000),
                    'id_estado' => 1,
                    'linc_transito' => (int)($empresa['NIT'] . $i),
                    'numero_chasis' => 'CHS' . $empresa['NIT'] . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'numero_motor' => 'MTR' . $empresa['NIT'] . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'doc_propietario' => $owner->doc_usuario,
                    'nombre_propietario' => $owner->primer_nombre . ' ' . $owner->primer_apellido,
                    'telefono' => $owner->telefono ?? '3000000000',
                    'correo' => $owner->correo
                ];
            }
            DB::table('bus')->insertOrIgnore($buses);
        }

    }
}
