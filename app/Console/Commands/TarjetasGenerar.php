<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tarjeta;
use Illuminate\Support\Facades\DB;
use App\Models\Usuario;

class TarjetasGenerar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tarjetas:generar {cantidad=20 : Cantidad de tarjetas a generar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera tarjetas virtuales en lote para stock inicial o pruebas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cantidadSolicitada = (int) $this->argument('cantidad');

        // Contar usuarios (pasajeros)
        $totalUsuarios = Usuario::count();
        // Contar tarjetas activas y sin asignar
        $stockDisponible = Tarjeta::whereNull('doc_usuario')
            ->whereHas('estado', function($q) {
                $q->where('nombre_estado', 'ACTIVO');
            })->count();

        // Calcular cuántas tarjetas crear para que stock > usuarios
        $aCrear = max(0, ($totalUsuarios + $cantidadSolicitada) - $stockDisponible);
        if ($aCrear <= 0) {
            $this->warn("No se necesitan nuevas tarjetas. Stock disponible: $stockDisponible, Usuarios: $totalUsuarios");
            return;
        }

        $creadas = 0;
        DB::transaction(function () use ($aCrear, &$creadas) {
            for ($i = 0; $i < $aCrear; $i++) {
                $tarjeta = new Tarjeta();
                $tarjeta->saldo = 0;
                $tarjeta->save();
                $creadas++;
            }
        });
        $stockFinal = Tarjeta::whereNull('doc_usuario')->whereHas('estado', function($q){$q->where('nombre_estado', 'ACTIVO');})->count();
        $this->info("Se generaron $creadas tarjetas. Stock final: $stockFinal");
    }
}
