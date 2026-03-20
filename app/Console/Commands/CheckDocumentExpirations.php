<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckDocumentExpirations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-document-expirations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Iniciando verificación de documentos vencidos...");

        // Cargar buses activos
        $buses = \App\Models\Bus::where('id_estado', 1)->get(); 
        $count = 0;

        foreach ($buses as $bus) {
            // isOperable() ya valida vencimiento y estado aprobado
            if (!$bus->isOperable()) {
                $bus->id_estado = 2; // INACTIVO
                $bus->save();
                $count++;
                $this->warn("Bus {$bus->placa} ha sido INACTIVADO por documentos vencidos o no aprobados.");
            }
        }

        $this->info("Verificación completada. {$count} buses de baja por expiración.");
    }
}
