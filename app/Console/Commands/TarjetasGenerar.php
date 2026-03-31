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

        // Buscar el id del estado INACTIVO
        $idEstadoInactivo = DB::table('estado')->where('nombre_estado', 'INACTIVO')->value('id_estado');
        if (!$idEstadoInactivo) {
            $this->error('No se encontró el estado INACTIVO en la tabla estado.');
            return;
        }

        // Buscar el último código de tarjeta numérico existente
        $ultimoCodigo = Tarjeta::max(DB::raw('CAST(codigo_tarjeta AS UNSIGNED)'));
        $nuevoCodigo = $ultimoCodigo ? ((int)$ultimoCodigo + 1) : 1000001;

        $creadas = 0;
        DB::transaction(function () use ($cantidadSolicitada, $idEstadoInactivo, &$creadas, &$nuevoCodigo) {
            for ($i = 0; $i < $cantidadSolicitada; $i++) {
                $tarjeta = new Tarjeta();
                $tarjeta->saldo = 0;
                $tarjeta->id_estado = $idEstadoInactivo;
                $tarjeta->codigo_tarjeta = (string)$nuevoCodigo;
                $tarjeta->save();
                $creadas++;
                $nuevoCodigo++;
            }
        });
        $stockFinal = Tarjeta::where('id_estado', $idEstadoInactivo)
            ->whereDoesntHave('titularidades', function($q) {
                $q->where('id_estado', 1); // Sin titularidad activa
            })
            ->count();
        $this->info("Se generaron $creadas tarjetas INACTIVAS. Stock final inactivo y sin titularidad activa: $stockFinal");
    }
}
