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
    protected $signature = 'tarjetas:generar {threshold=10 : Stock mínimo de tarjetas disponibles a mantener}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mantiene un stock mínimo de tarjetas virtuales inactivas y disponibles.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $threshold = (int) $this->argument('threshold');

        // Buscar el id del estado INACTIVO
        $idEstadoInactivo = DB::table('estado')->where('nombre_estado', 'INACTIVO')->value('id_estado');
        if (!$idEstadoInactivo) {
            $this->error('No se encontró el estado INACTIVO en la tabla estado.');
            return;
        }

        // 1. Contar stock actual disponible (Inactivas y sin titularidades activas)
        $stockActual = Tarjeta::where('id_estado', $idEstadoInactivo)
            ->whereDoesntHave('titularidades', function($q) {
                // Consideramos stock disponible aquellas inactivas sin ninguna titularidad activa (id_estado 1 en titularidad)
                $q->where('id_estado', 1);
            })
            ->count();

        if ($stockActual >= $threshold) {
            $this->info("Stock suficiente. Hay $stockActual tarjetas disponibles (Umbral: $threshold). No se generaron nuevas.");
            return;
        }

        $cantidadAGenerar = $threshold - $stockActual;
        $this->info("Stock bajo ($stockActual/$threshold). Generando $cantidadAGenerar tarjetas nuevas...");

        // Buscar el último código de tarjeta numérico existente para seguir la secuencia (o iniciar en 100000000001)
        $ultimoCodigo = Tarjeta::max(DB::raw('CAST(codigo_tarjeta AS UNSIGNED)'));
        $nuevoCodigo = $ultimoCodigo ? ((int)$ultimoCodigo + 1) : 100000000001;

        $creadas = 0;
        DB::transaction(function () use ($cantidadAGenerar, $idEstadoInactivo, &$creadas, &$nuevoCodigo) {
            for ($i = 0; $i < $cantidadAGenerar; $i++) {
                $idAlfanumerico = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(12));

                $tarjeta = new Tarjeta();
                $tarjeta->id_tarjeta = $idAlfanumerico; 
                $tarjeta->codigo_tarjeta = (string)$nuevoCodigo; 
                $tarjeta->saldo = 0; 
                $tarjeta->id_estado = $idEstadoInactivo; 
                $tarjeta->save();

                $creadas++;
                $nuevoCodigo++;
            }
        });

        $this->info("¡Éxito! Se completó el stock con $creadas tarjetas nuevas.");
        $this->info("Stock total disponible de tarjetas inactivas: " . ($stockActual + $creadas));
    }
}
