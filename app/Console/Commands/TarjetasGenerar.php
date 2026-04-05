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

        // Buscar el último código de tarjeta numérico existente para seguir la secuencia (o iniciar en 100000000001)
        $ultimoCodigo = Tarjeta::max(DB::raw('CAST(codigo_tarjeta AS UNSIGNED)'));
        $nuevoCodigo = $ultimoCodigo ? ((int)$ultimoCodigo + 1) : 100000000001;

        $creadas = 0;
        DB::transaction(function () use ($cantidadSolicitada, $idEstadoInactivo, &$creadas, &$nuevoCodigo) {
            for ($i = 0; $i < $cantidadSolicitada; $i++) {
                // Generar un id_tarjeta alfanumérico aleatorio (12 caracteres) conforme al modelo
                // Nota: Usamos Str::random() que genera [a-zA-Z0-9]. 
                // Lo forzamos a mayúsculas para un formato de "serial" más estándar si lo prefieres,
                // de lo contrario usamos Str::random directamente.
                $idAlfanumerico = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(12));

                // Al crear la instancia, asignamos los valores fijos solicitados
                $tarjeta = new Tarjeta();
                $tarjeta->id_tarjeta = $idAlfanumerico; // ID Alfanumérico (letras y números)
                $tarjeta->codigo_tarjeta = (string)$nuevoCodigo; // Código de barras numérico secuencial
                $tarjeta->saldo = 0; // Saldo inicial CERO
                $tarjeta->id_estado = $idEstadoInactivo; // Siempre INACTIVA hasta asignarle titularidad
                $tarjeta->save();

                $creadas++;
                $nuevoCodigo++;
            }
        });

        $stockFinal = Tarjeta::where('id_estado', $idEstadoInactivo)
            ->whereDoesntHave('titularidades', function($q) {
                // Consideramos stock disponible aquellas inactivas sin ninguna titularidad activa (id_estado 1 en titularidad)
                $q->where('id_estado', 1);
            })
            ->count();

        $this->info("¡Éxito! Se generaron $creadas tarjetas nuevas.");
        $this->info(" - id_tarjeta: Alfanumérico aleatorio (ej: " . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(12)) . ")");
        $this->info(" - codigo_tarjeta: Secuencial (ej: $nuevoCodigo)");
        $this->info(" - Saldo: 0");
        $this->info(" - Estado: INACTIVA");
        $this->info("Stock total disponible de tarjetas inactivas: $stockFinal");
    }
}
