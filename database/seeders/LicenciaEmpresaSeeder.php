<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LicenciaEmpresaSeeder extends Seeder
{
    public function run()
    {
        DB::table('licencias')->insertOrIgnore([
            [
                'id_licencia' => 'LIC-900123456-2026',
                'NIT' => 900123456,
                'id_plan' => 1, // Básico
                'fecha_inicio' => now()->toDateString(),
                'fecha_vencimiento' => now()->addMonths(12)->toDateString(),
                'id_estado' => 1,
<<<<<<< HEAD
                'doc_super_admin' => 1105463369,
=======
                'doc_super_admin' => 1000000001,
>>>>>>> develop
                'fecha_creacion' => now(),
            ]
        ]);
    }
}
