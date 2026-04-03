<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FixTarjetaAlfanumericaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Obtener todas las tarjetas con IDs puramente numéricos
        $tarjetas = DB::table('tarjeta')
            ->where('id_tarjeta', 'REGEXP', '^[0-9]+$')
            ->get();

        if ($tarjetas->isEmpty()) {
            $this->command->info('No se encontraron tarjetas con IDs puramente numéricos.');
            return;
        }

        $this->command->info('Actualizando ' . $tarjetas->count() . ' tarjetas a formato alfanumérico aleatorio...');

        // 2. Desactivar temporalmente las claves foráneas
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            DB::transaction(function () use ($tarjetas) {
                foreach ($tarjetas as $tarjeta) {
                    $oldId = $tarjeta->id_tarjeta;
                    
                    // Generar un nuevo ID alfanumérico aleatorio (12 caracteres)
                    // Nos aseguramos de que no exista ya en la base de datos
                    do {
                        $newId = Str::upper(Str::random(12));
                    } while (DB::table('tarjeta')->where('id_tarjeta', $newId)->exists());

                    // Actualizar en todas las tablas relacionadas
                    // tarjeta (Tabla Principal)
                    DB::table('tarjeta')->where('id_tarjeta', $oldId)->update(['id_tarjeta' => $newId]);

                    // titularidad_tarjeta
                    DB::table('titularidad_tarjeta')->where('id_tarjeta', $oldId)->update(['id_tarjeta' => $newId]);

                    // recarga
                    DB::table('recarga')->where('id_tarjeta', $oldId)->update(['id_tarjeta' => $newId]);

                    // venta_viaje
                    DB::table('venta_viaje')->where('id_tarjeta', $oldId)->update(['id_tarjeta' => $newId]);
                }
            });

            $this->command->info('¡Actualización completada con éxito!');
        } catch (\Exception $e) {
            $this->command->error('Error durante la actualización: ' . $e->getMessage());
        } finally {
            // 3. Reactivar las claves foráneas
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}
