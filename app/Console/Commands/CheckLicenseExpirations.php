<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CheckLicenseExpirations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-license-expirations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza el estado de las licencias y empresas cuando la fecha de vencimiento ha pasado.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Iniciando verificación de licencias vencidas...");
        $today = Carbon::today()->toDateString();

        // 1. Obtener NITs de empresas con licencias activas pero vencidas por fecha
        $nitsVencidos = DB::table('licencias')
            ->where('id_estado', 1) // ACTIVA
            ->where('fecha_vencimiento', '<', $today)
            ->pluck('NIT');

        if ($nitsVencidos->isEmpty()) {
            $this->info("No se encontraron licencias nuevas por vencer hoy.");
            return;
        }

        $countLicencias = 0;
        $countEmpresas = 0;

        foreach ($nitsVencidos as $nit) {
            // Actualizar LICENCIAS de esta empresa que estén vencidas
            $updatedLicencias = DB::table('licencias')
                ->where('NIT', $nit)
                ->where('id_estado', 1)
                ->where('fecha_vencimiento', '<', $today)
                ->update(['id_estado' => 8]); // VENCIDA

            $countLicencias += $updatedLicencias;

            // Opcional: Inactivar la EMPRESA si no tiene ninguna otra licencia vigente
            // (En este sistema usualmente solo hay una licencia principal activa)
            $tieneOtrasActivas = DB::table('licencias')
                ->where('NIT', $nit)
                ->where('id_estado', 1)
                ->where('fecha_vencimiento', '>=', $today)
                ->exists();

            if (!$tieneOtrasActivas) {
                DB::table('empresa')
                    ->where('NIT', $nit)
                    ->update(['id_estado' => 2]); // INACTIVO
                $countEmpresas++;
                $this->warn("Empresa NIT {$nit} ha sido INACTIVADA por falta de licencia vigente.");
            }
        }

        $this->info("Proceso completado.");
        $this->info("- Licencias marcadas como vencidas: {$countLicencias}");
        $this->info("- Empresas inactivadas: {$countEmpresas}");
    }
}
