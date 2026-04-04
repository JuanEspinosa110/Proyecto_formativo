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
            
            if ($index === 0) {
                // 🔴 Generar una licencia VENCIDA por fecha para la primera empresa
                $fecha_inicio = Carbon::now()->subMonths($plan->duracion_meses + 1);
                $fecha_vencimiento = Carbon::now()->subDays(5);
            } elseif ($index === 1) {
                // 🟡 Generar una licencia POR VENCER (en 10 días)
                $fecha_inicio = Carbon::now()->subMonths($plan->duracion_meses)->addDays(10);
                $fecha_vencimiento = Carbon::now()->addDays(10);
            } else {
                // 🟢 Generar una licencia VIGENTE normal
                $fecha_inicio = Carbon::now();
                $fecha_vencimiento = $fecha_inicio->copy()->addMonths($plan->duracion_meses);
            }

            DB::table('licencias')->insertOrIgnore([
                'id_licencia' => 'LIC-' . $empresa->NIT . '-' . ($index + 1),
                'NIT' => $empresa->NIT,
                'id_plan' => $plan->id_plan,
                'fecha_inicio' => $fecha_inicio->format('Y-m-d'),
                'fecha_vencimiento' => $fecha_vencimiento->format('Y-m-d'),
                'id_estado' => 1, // Se crean como "Activas" para probar la auto-expiración
                'doc_super_admin' => 1105463369,
                'fecha_creacion' => Carbon::now()->subMonths(1),
            ]);
        }
    }
}
