<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LicenciaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener solo las empresas tipo transporte (id_tipo_empresa = 1)
        $empresas = DB::table('empresa')->where('id_tipo_empresa', 1)->get();
        
        $planes = DB::table('planes_licencia')->get();

        if ($empresas->isEmpty() || $planes->isEmpty()) {
            return;
        }

        foreach ($empresas as $index => $empresa) {
            // Seleccionar un plan aleatorio
            $plan = $planes->random();
            $fecha_inicio = Carbon::now();
            $fecha_vencimiento = $fecha_inicio->copy()->addMonths($plan->duracion_meses);

            DB::table('licencias')->insert([
                'id_licencia' => 'LIC-' . $empresa->NIT . '-' . ($index + 1),
                'NIT' => $empresa->NIT,
                'id_plan' => $plan->id_plan,
                'fecha_inicio' => $fecha_inicio->format('Y-m-d'),
                'fecha_vencimiento' => $fecha_vencimiento->format('Y-m-d'),
                'id_estado' => 1,
                'doc_super_admin' => 1105463369, // Del SuperAdministradorSeeder
                'fecha_creacion' => Carbon::now(),
            ]);
        }
    }
}
