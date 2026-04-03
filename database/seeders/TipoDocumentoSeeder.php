<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoDocumentoSeeder extends Seeder
{
    public function run()
    {
        DB::table('tipo_documento')->insertOrIgnore([
            [
                'id_tipo_documento' => 1,
                'nombre' => 'SOAT',
                'descripcion' => 'Seguro Obligatorio de Accidentes de Tránsito',
                'requiere_doc_usuario' => 0,
                'requiere_placa' => 1,
                'id_estado' => 1
            ],
            [
                'id_tipo_documento' => 2,
                'nombre' => 'Técnico Mecánica',
                'descripcion' => 'Certificado de revisión técnico-mecánica',
                'requiere_doc_usuario' => 0,
                'requiere_placa' => 1,
                'id_estado' => 1
            ],
            [
                'id_tipo_documento' => 3,
                'nombre' => 'Licencia de Tránsito',
                'descripcion' => 'Licencia de conducción del vehículo',
                'requiere_doc_usuario' => 1,
                'requiere_placa' => 0,
                'id_estado' => 1
            ]
        ]);
    }
}
