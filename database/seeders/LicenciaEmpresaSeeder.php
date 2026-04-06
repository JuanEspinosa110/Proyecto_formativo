<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LicenciaEmpresaSeeder extends Seeder
{
    public function run()
    {
        DB::table('licencias')->updateOrInsert(
            ['id_licencia' => 'LIC-900123456-2026'],
            [
                'NIT' => 900123456,
                'id_plan' => 1, // Básico
                'fecha_inicio' => now()->subMonths(11)->toDateString(),
                'fecha_vencimiento' => now()->addDays(20)->toDateString(), // Menos de 29 días
                'id_estado' => 1, // ACTIVO (Aún no vence)
                'doc_super_admin' => 1105463369,
                'fecha_creacion' => now()->subMonths(11),
            ]
        );
    }
}
