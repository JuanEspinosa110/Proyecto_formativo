<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusSeeder extends Seeder
{
    public function run()
    {
        // Buses para propietario: Pedro Luis Gomez (doc_propietario: 2000000002)
        DB::table('bus')->insertOrIgnore([
            [
                'placa' => 'ABC123',
                'NIT' => 900123456,
                'modelo' => 'Mercedes 2020',
                'capacidad_pasajeros' => 40,
                'kilometraje' => 50000,
                'id_estado' => 1,
                'linc_transito' => 123456789,
                'numero_chasis' => 'CHS123456789',
                'numero_motor' => 'MTR123456789',
                'doc_propietario' => 2000000002,
                'nombre_propietario' => 'Pedro Luis Gomez Propietario',
                'telefono' => '3001112222',
                'correo' => 'propietario@empresa.com'
            ],
            [
                'placa' => 'DEF456',
                'NIT' => 900123456,
                'modelo' => 'Volvo 2018',
                'capacidad_pasajeros' => 35,
                'kilometraje' => 75000,
                'id_estado' => 1,
                'linc_transito' => 987654321,
                'numero_chasis' => 'CHS987654321',
                'numero_motor' => 'MTR987654321',
                'doc_propietario' => 2000000002,
                'nombre_propietario' => 'Pedro Luis Gomez Propietario',
                'telefono' => '3001112222',
                'correo' => 'propietario@empresa.com'
            ],
            [
                'placa' => 'GHI789',
                'NIT' => 900123456,
                'modelo' => 'Scania 2019',
                'capacidad_pasajeros' => 42,
                'kilometraje' => 60000,
                'id_estado' => 1,
                'linc_transito' => 192837465,
                'numero_chasis' => 'CHS192837465',
                'numero_motor' => 'MTR192837465',
                'doc_propietario' => 2000000002,
                'nombre_propietario' => 'Pedro Luis Gomez Propietario',
                'telefono' => '3001112222',
                'correo' => 'propietario@empresa.com'
            ],
            [
                'placa' => 'JKL012',
                'NIT' => 900123456,
                'modelo' => 'Chevrolet 2021',
                'capacidad_pasajeros' => 38,
                'kilometraje' => 40000,
                'id_estado' => 1,
                'linc_transito' => 564738291,
                'numero_chasis' => 'CHS564738291',
                'numero_motor' => 'MTR564738291',
                'doc_propietario' => 2000000002,
                'nombre_propietario' => 'Pedro Luis Gomez Propietario',
                'telefono' => '3001112222',
                'correo' => 'propietario@empresa.com'
            ]
        ]);

        // Buses para propietario: Laura Sofia Martinez (doc_propietario: 2000000003)
        DB::table('bus')->insertOrIgnore([
            [
                'placa' => 'MNO345',
                'NIT' => 900123456,
                'modelo' => 'Volkswagen 2022',
                'capacidad_pasajeros' => 36,
                'kilometraje' => 30000,
                'id_estado' => 1,
                'linc_transito' => 111222333,
                'numero_chasis' => 'CHS111222333',
                'numero_motor' => 'MTR111222333',
                'doc_propietario' => 2000000003,
                'nombre_propietario' => 'Laura Sofia Martinez Propietaria',
                'telefono' => '3002223333',
                'correo' => 'propietaria2@empresa.com'
            ],
            [
                'placa' => 'PQR678',
                'NIT' => 900123456,
                'modelo' => 'Renault 2021',
                'capacidad_pasajeros' => 32,
                'kilometraje' => 45000,
                'id_estado' => 1,
                'linc_transito' => 444555666,
                'numero_chasis' => 'CHS444555666',
                'numero_motor' => 'MTR444555666',
                'doc_propietario' => 2000000003,
                'nombre_propietario' => 'Laura Sofia Martinez Propietaria',
                'telefono' => '3002223333',
                'correo' => 'propietaria2@empresa.com'
            ],
            [
                'placa' => 'STU901',
                'NIT' => 900123456,
                'modelo' => 'Toyota 2020',
                'capacidad_pasajeros' => 39,
                'kilometraje' => 52000,
                'id_estado' => 1,
                'linc_transito' => 777888999,
                'numero_chasis' => 'CHS777888999',
                'numero_motor' => 'MTR777888999',
                'doc_propietario' => 2000000003,
                'nombre_propietario' => 'Laura Sofia Martinez Propietaria',
                'telefono' => '3002223333',
                'correo' => 'propietaria2@empresa.com'
            ],
            [
                'placa' => 'VWX234',
                'NIT' => 900123456,
                'modelo' => 'Hyundai 2019',
                'capacidad_pasajeros' => 34,
                'kilometraje' => 61000,
                'id_estado' => 1,
                'linc_transito' => 222333444,
                'numero_chasis' => 'CHS222333444',
                'numero_motor' => 'MTR222333444',
                'doc_propietario' => 2000000003,
                'nombre_propietario' => 'Laura Sofia Martinez Propietaria',
                'telefono' => '3002223333',
                'correo' => 'propietaria2@empresa.com'
            ]
        ]);
    }
}
